<?php

namespace Projects\WellmedBackbone\Imports;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithChunkReading,
    WithHeadingRow,
    WithMultipleSheets
};
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientImport implements
    ToCollection,
    WithChunkReading,
    WithHeadingRow,
    WithMultipleSheets
{
    use HasRequestData;

    protected string $importId;
    protected int $chunkNumber = 0;
    protected int $processedRows = 0;

    public function __construct(?string $importId = null)
    {
        $this->importId = $importId ?? uniqid('patient_import_', true);
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

    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        $this->chunkNumber++;
        $chunkStartRow = $this->processedRows + 2; // +2 because row 1 is header

        Log::channel('import')->info('=== CHUNK START ===', [
            'import_id' => $this->importId,
            'chunk_number' => $this->chunkNumber,
            'chunk_rows' => $rows->count(),
            'chunk_start_row' => $chunkStartRow,
        ]);

        // Load encoding counter from DB at the start of each chunk
        // This ensures we have the latest counter value (persisted from previous chunk)
        app(EncodingWrapper::class)->installationSetup();
        config(['module-patient.satu-sehat.enable' => false]);

        foreach ($rows as $index => $row) {
            $rowNumber = $chunkStartRow + $index; // Actual row number in Excel file

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
             * 2. EXTRACT IDENTIFIERS
             * ======================
             */
            $emrValue = isset($row['no_emr']) && !empty($row['no_emr']) ? (string) $row['no_emr'] : null;
            $nikValue = isset($row['nik']) && !empty($row['nik']) ? (string) $row['nik'] : null;

            /**
             * ======================
             * 3. DEDUP DI DATABASE
             * ======================
             * With chunking, we rely on database checks for deduplication
             * since in-memory arrays don't persist across chunks
             */
            if ($emrValue || $nikValue) {
                if ($this->existsInDatabase($row)) {
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
                            // medical_record will be auto-generated by Patient schema
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

        // Update processed rows counter for next chunk
        $this->processedRows += $rows->count();

        // CRITICAL: Persist encoding counter to database AFTER each chunk
        // This ensures the next chunk will load the correct counter value
        app(EncodingWrapper::class)->setup();

        Log::channel('import')->info('=== CHUNK END ===', [
            'import_id' => $this->importId,
            'chunk_number' => $this->chunkNumber,
            'total_processed_rows' => $this->processedRows,
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

    public function getImportId(): string
    {
        return $this->importId;
    }
}
