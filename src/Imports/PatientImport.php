<?php

namespace Projects\WellmedBackbone\Imports;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithMultipleSheets
};
use Hanafalah\ModuleEncoding\Concerns\HasEncoding;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithMultipleSheets
{
    use HasRequestData;

    protected string $importId;
    protected int $chunkNumber = 0;
    protected const REDIS_TTL = 86400; // 24 hours

    public function __construct(?string $importId = null)
    {
        $this->importId = $importId ?? uniqid('patient_import_', true);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Only process the first sheet (index 0)
     * This prevents duplicate processing when Excel file has multiple sheets
     */
    public function sheets(): array
    {
        return [
            0 => $this, // Only process sheet at index 0
        ];
    }

    public function collection(Collection $rows)
    {
        $this->chunkNumber++;
        $chunkStart = (($this->chunkNumber - 1) * $this->chunkSize()) + 2; // +2 karena header di row 1
        $chunkEnd = $chunkStart + $rows->count() - 1;

        Log::channel('import')->info('=== CHUNK START ===', [
            'import_id' => $this->importId,
            'chunk' => $this->chunkNumber,
            'rows_in_chunk' => $rows->count(),
            'row_range' => "{$chunkStart}-{$chunkEnd}",
            'redis_mr_count' => $this->getRedisSetCount('mr'),
            'redis_nik_count' => $this->getRedisSetCount('nik'),
        ]);

        app(EncodingWrapper::class)->installationSetup();
        config(['module-patient.satu-sehat.enable' => false]);

        foreach ($rows as $index => $row) {
            $rowNumber = $chunkStart + $index; // Row number absolut dari Excel

            /**
             * ======================
             * 1. VALIDASI WAJIB
             * ======================
             */
            if (!isset($row['nama'])) {
                $this->logSkip($rowNumber, 'nama kosong');
                continue;
            }

            /**
             * ======================
             * 2. DEDUP VIA REDIS (persist across chunks & retries)
             * ======================
             */
            $is_has_emr = false;
            $emrValue = isset($row['no_emr']) && !empty($row['no_emr']) ? (string) $row['no_emr'] : null;
            if ($emrValue) {
                $is_has_emr = true;
                if ($this->isInRedisSet('mr', $emrValue)) {
                    $this->logSkip($rowNumber, 'duplicate MR (redis)', $emrValue);
                    continue;
                }
            }

            $is_has_nik = false;
            $nikValue = isset($row['nik']) && !empty($row['nik']) ? (string) $row['nik'] : null;
            if ($nikValue) {
                $is_has_nik = true;
                if ($this->isInRedisSet('nik', $nikValue)) {
                    $this->logSkip($rowNumber, 'duplicate NIK (redis)', $nikValue);
                    continue;
                }
            }

            /**
             * ======================
             * 3. DEDUP DI DATABASE
             * ======================
             */
            if ($is_has_emr || $is_has_nik) {
                if ($this->existsInDatabase($row)) {
                    // Add to Redis so we don't check DB again for same value
                    if ($emrValue) $this->addToRedisSet('mr', $emrValue);
                    if ($nikValue) $this->addToRedisSet('nik', $nikValue);

                    $this->logSkip($rowNumber, 'MR / NIK sudah ada di database', [
                        'no_emr' => $emrValue,
                        'nik' => $nikValue,
                    ]);
                    continue;
                }
            }
            /**
             * ======================
             * 4. NORMALISASI DATA
             * ======================
             */
            if (isset($row['tanggal_lahir']) && is_numeric($row['tanggal_lahir'])) {
                $row['tanggal_lahir'] = Date::excelToDateTimeObject(
                    $row['tanggal_lahir']
                )->format('Y-m-d');
            }

            [$firstName, $lastName] = array_pad(
                explode(' ', $row['nama'], 2),
                2,
                null
            );

            if (isset($firstName) && !isset($lastName)){
                $lastName = $firstName;
                $firstName = null;
            }else{
                if (!isset($firstName) && !isset($lastName)){
                    $lastName = $row['nama'];
                }
            }

            /**
             * ======================
             * 5. STORE
             * ======================
             * Each row is wrapped in its own transaction to ensure proper isolation.
             * If one row fails, subsequent rows can still be processed.
             */
            try {
                request()->replace([]);
                DB::transaction(function () use ($row, $firstName, $lastName, $rowNumber, $emrValue, $nikValue) {
                    $nik = $nikValue;
                    app(config('app.contracts.Patient'))->prepareStorePatient(
                        $this->requestDTO(config('app.contracts.PatientData'), [
                            'id' => null,
                            'card_identity' => [
                                'old_mr'     => $emrValue,
                                'ihs_number' => $row['ihs_satu_sehat'] ?? null,
                                'bpjs'       => $row['no_bpjs'] ?? null,
                            ],
                            'name' => $row['nama'],
                            'medical_record' => HasEncoding::generateCode('MEDICAL_RECORD'),
                            'reference_type' => 'People',
                            'reference' => [
                                'first_name' => $firstName,
                                'last_name'  => $lastName,
                                'phone_1'    => $row['kontakhp'] ?? null,
                                'blood_type' => $row['golongan_darah'] ?? null,
                                'pob'        => $row['tempat_lahir'] ?? null,
                                'dob'        => $row['tanggal_lahir'] ?? null,
                                'sex'        => $row['jenis_kelamin'] ?? null,
                                'address' => [
                                    'ktp' => ['name' => $row['alamat_ktp'] ?? null],
                                    'residence' => ['name' => $row['alamat_domisili'] ?? null],
                                ],
                                'card_identity' => [
                                    'nik'      => $nik,
                                    'passport' => $row['passport'] ?? null,
                                ],
                            ],
                        ])
                    );

                    Log::channel('import')->info('Patient imported', [
                        'row' => $rowNumber,
                        'name' => $row['nama'],
                    ]);
                });

                // Add to Redis AFTER successful insert
                if ($emrValue) $this->addToRedisSet('mr', $emrValue);
                if ($nikValue) $this->addToRedisSet('nik', $nikValue);

            } catch (\Throwable $e) {
                Log::channel('import')->error('Failed import patient', [
                    'row' => $rowNumber,
                    'name' => $row['nama'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('import')->info('=== CHUNK END ===', [
            'import_id' => $this->importId,
            'chunk' => $this->chunkNumber,
            'redis_mr_count' => $this->getRedisSetCount('mr'),
            'redis_nik_count' => $this->getRedisSetCount('nik'),
        ]);
    }

    /**
     * ======================
     * HELPERS
     * ======================
     */
    protected function existsInDatabase(Collection $row): bool
    {
        $query = app(config('database.models.CardIdentity'))::query();

        // Fix: Logic was inverted - should check when value IS set, not when NOT set
        if (isset($row['no_emr'])) {
            $query->orWhere(function($q) use ($row){
                $q->where('flag', 'old_mr')->where('value', (string) $row['no_emr']);
            });
        }

        if (isset($row['nik'])) {
            $query->orWhere(function($q) use ($row){
                $q->where('flag', 'nik')->where('value', (string) $row['nik']);
            });
        }

        return $query->exists();
    }

    protected function logSkip(int $row, string $reason, $value = null): void
    {
        Log::channel('import')->warning('Skipping patient row', [
            'row'    => $row,
            'reason' => $reason,
            'value'  => $value,
        ]);
    }

    /**
     * ======================
     * REDIS HELPERS
     * ======================
     */
    protected function getRedisKey(string $type): string
    {
        return "patient_import:{$this->importId}:{$type}";
    }

    protected function isInRedisSet(string $type, string $value): bool
    {
        return (bool) Redis::sismember($this->getRedisKey($type), $value);
    }

    protected function addToRedisSet(string $type, string $value): void
    {
        $key = $this->getRedisKey($type);
        Redis::sadd($key, $value);
        Redis::expire($key, self::REDIS_TTL);
    }

    protected function getRedisSetCount(string $type): int
    {
        return (int) Redis::scard($this->getRedisKey($type));
    }

    /**
     * Clean up Redis keys after import completes
     */
    public function cleanupRedis(): void
    {
        Redis::del($this->getRedisKey('mr'));
        Redis::del($this->getRedisKey('nik'));
        Log::channel('import')->info('Redis cleanup completed', ['import_id' => $this->importId]);
    }

    public function getImportId(): string
    {
        return $this->importId;
    }
}
