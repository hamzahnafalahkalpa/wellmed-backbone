<?php

namespace Projects\WellmedBackbone\Imports;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithStartRow,
    WithMultipleSheets
};
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithStartRow,
    WithMultipleSheets
{
    use HasRequestData;

    protected array $seenMr  = [];
    protected array $seenNik = [];
    protected int $chunkNumber = 0;

    public function startRow(): int
    {
        return 2;
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
            'chunk' => $this->chunkNumber,
            'rows_in_chunk' => $rows->count(),
            'row_range' => "{$chunkStart}-{$chunkEnd}",
            'seenMr_count' => count($this->seenMr),
            'seenNik_count' => count($this->seenNik),
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

            // if (!isset($row['no_emr']) && !isset($row['nik'])) {
            //     $this->logSkip($rowNumber, 'MR dan NIK sama-sama kosong');
            //     continue;
            // }

            /**
             * ======================
             * 2. DEDUP DALAM FILE
             * ======================
             */
            $is_has_emr = false;
            if (isset($row['no_emr'])) {
                $is_has_emr = true;
                if (isset($this->seenMr[$row['no_emr']])) {
                    $this->logSkip($rowNumber, 'duplicate MR dalam file', $row['no_emr']);
                    continue;
                }
                $this->seenMr[$row['no_emr']] = true;
            }

            $is_has_nik = false;
            if (isset($row['nik'])) {
                $is_has_nik = true;
                if (isset($this->seenNik[$row['nik']])) {
                    $this->logSkip($rowNumber, 'duplicate NIK dalam file', $row['nik']);
                    continue;
                }
                $this->seenNik[$row['nik']] = true;
            }

            /**
             * ======================
             * 3. DEDUP DI DATABASE
             * ======================
             */
            if ($is_has_emr || $is_has_nik) {
                if ($this->existsInDatabase($row)) {
                    $this->logSkip($rowNumber, 'MR / NIK sudah ada di database', [
                        'no_emr' => $row['no_emr'] ?? null,
                        'nik' => $row['nik'] ?? null,
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
                DB::transaction(function () use ($row, $firstName, $lastName, $rowNumber) {
                    $nik = $row['nik'] ?? null;
                    if (isset($nik)) $nik = (string) $nik;
                    app(config('app.contracts.Patient'))->prepareStorePatient(
                        $this->requestDTO(config('app.contracts.PatientData'), [
                            'id' => null,
                            'card_identity' => [
                                'old_mr'     => $row['no_emr'] ?? null,
                                'ihs_number' => $row['ihs_satu_sehat'] ?? null,
                                'bpjs'       => $row['no_bpjs'] ?? null,
                            ],
                            'name' => $row['nama'],
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
            } catch (\Throwable $e) {
                Log::channel('import')->error('Failed import patient', [
                    'row' => $rowNumber,
                    'name' => $row['nama'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::channel('import')->info('=== CHUNK END ===', [
            'chunk' => $this->chunkNumber,
            'seenMr_count' => count($this->seenMr),
            'seenNik_count' => count($this->seenNik),
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
        if (isset($row['no_emr']) && !empty($row['no_emr'])) {
            $query->orWhere(function($q) use ($row){
                $q->where('flag', 'old_mr')->where('value', (string) $row['no_emr']);
            });
        }

        if (isset($row['nik']) && !empty($row['nik'])) {
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
}
