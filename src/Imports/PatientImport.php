<?php

namespace Projects\WellmedBackbone\Imports;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithStartRow
};
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithStartRow
{
    use HasRequestData;

    protected array $seenMr  = [];
    protected array $seenNik = [];

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        app(EncodingWrapper::class)->installationSetup();
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // biar sesuai excel

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
            if ($is_has_emr || $is_has_nik){
                if ($this->existsInDatabase($row)) {
                    $this->logSkip($rowNumber, 'MR / NIK sudah ada di database');
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
             */
            try {
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
            } catch (\Throwable $e) {
                Log::channel('import')->error('Failed import patient', [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * ======================
     * HELPERS
     * ======================
     */
    protected function existsInDatabase(Collection $row): bool
    {
        $query = app(config('database.models.CardIdentity'))::query();

        if (!isset($row['no_emr'])) {
            $query->orWhere(function($query) use ($row){
                $query->where('flag','old_mr')->where('value', (string) $row['no_emr']);
            });
        }

        if (!isset($row['nik'])) {
            $query->orWhere(function($query) use ($row){
                $query->where('flag','nik')->where('value', (string) $row['nik']);
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
