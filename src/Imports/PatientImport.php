<?php

namespace Projects\WellmedBackbone\Imports;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithChunkReading,
    WithHeadingRow,
    RemembersChunkOffset
};
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientImport implements
    ToCollection,
    WithChunkReading,
    WithHeadingRow,
    ShouldQueue
{
    use HasRequestData, RemembersChunkOffset;

    protected string $importId;

    /**
     * Tenant ID for multi-tenant context restoration in queued chunks.
     * This gets serialized with the job and restored in each chunk.
     */
    protected ?int $tenantId = null;

    public function __construct(?string $importId = null, ?int $tenantId = null)
    {
        $this->importId = $importId ?? uniqid('patient_import_', true);
        $this->tenantId = $tenantId;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        // Use RemembersChunkOffset trait for accurate row tracking across queued chunks
        // With WithHeadingRow, getChunkOffset() returns the 1-based Excel row number
        // (e.g., 2 for first data row since row 1 is the header)
        $chunkStartRow = $this->getChunkOffset();

        Log::channel('import')->info('=== CHUNK START ===', [
            'import_id' => $this->importId,
            'chunk_offset' => $this->getChunkOffset(),
            'chunk_rows' => $rows->count(),
            'chunk_start_row' => $chunkStartRow,
            'tenant_id' => $this->tenantId,
        ]);

        // CRITICAL: Restore tenant context for each queued chunk
        // Each chunk runs as a separate job and loses the tenant context
        if ($this->tenantId) {
            \Hanafalah\MicroTenant\Facades\MicroTenant::tenantImpersonate($this->tenantId);
        }

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
                    $create = [
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
                        ];
                    if (isset($row['patient_occupation'])){
                        $create['patient_occupation'] = [
                            'id' => null,
                            'name' => $row['patient_occupation']
                        ];
                    }
                    if (isset($row['allergy']) || isset($row['old_visit_summary'])){
                        $old_visit = [
                            'allergy' => $row['allergy'] ?? null,
                            'emr' => $row['old_visit_summary'] ?? null,
                        ];
                        $create['old_visit'] = $old_visit;
                    }
                    app(config('app.contracts.Patient'))->prepareStorePatient(
                        $this->requestDTO(config('app.contracts.PatientData'), $create)
                    );

                    Log::channel('import')->info('Patient imported', [
                        'row' => $rowNumber,
                        'name' => $row['nama'],
                    ]);
                });

            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Race condition: another process inserted this record between our check and insert
                // This is expected behavior with concurrent imports, log as warning not error
                Log::channel('import')->warning('Patient already exists (race condition)', [
                    'row' => $rowNumber,
                    'name' => $row['nama'] ?? 'unknown',
                    'no_emr' => $emrValue,
                    'nik' => $nikValue,
                ]);
            } catch (\Throwable $e) {
                Log::channel('import')->error('Failed import patient', [
                    'row' => $rowNumber,
                    'name' => $row['nama'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // CRITICAL: Persist encoding counter to database AFTER each chunk
        // This ensures the next chunk will load the correct counter value
        app(EncodingWrapper::class)->setup();

        Log::channel('import')->info('=== CHUNK END ===', [
            'import_id' => $this->importId,
            'chunk_offset' => $this->getChunkOffset(),
            'rows_in_chunk' => $rows->count(),
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
