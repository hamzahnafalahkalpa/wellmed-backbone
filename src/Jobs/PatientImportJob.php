<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PatientImportJob implements ShouldQueue
{
    use Queueable, SerializesModels, HasRequestData;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $support = $this->data['support'] ?? [];
        $paths = $support['paths'] ?? [];
        MicroTenant::tenantImpersonate($this->data['tenant_id']);
        foreach ($paths as $path) {
            $filePath = $this->resolveFilePath($path);

            $datas = Excel::toArray(
                new class implements \Maatwebsite\Excel\Concerns\ToArray, 
                    \Maatwebsite\Excel\Concerns\WithStartRow,
                    \Maatwebsite\Excel\Concerns\WithHeadingRow {
                    public function startRow(): int {
                        return 2;
                    }
                    public function array(array $array) {}
                },
                $filePath
            )[0];
            // \Log::channel('import')->info('Patient import job processing', ['filePath' => $filePath, 'total_rows' => count($datas)]);

            // Proses data sesuai kebutuhan
            // foreach (array_chunk($datas, 100) as $data) {
            foreach ($datas as $data) {
                if (!isset($data['nama'])) continue;
                $name_parts = explode(' ', $data['nama'] ?? '', 2);
                $first_name = $name_parts[0] ?? '';
                $last_name = $name_parts[1] ?? '';
                // Konversi tanggal dari Excel serial number ke format Y-m-d
                if (isset($data['tanggal_lahir']) && is_numeric($data['tanggal_lahir'])) {
                    $data['tanggal_lahir'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['tanggal_lahir'])->format('Y-m-d');
                }
                \Log::channel('import')->info('Storing patient', ['name' => $data['nama'] ?? null, 'dob' => $data['tanggal_lahir'] ?? null]);
                app(config('app.contracts.Patient'))->prepareStorePatient(
                    $this->requestDTO(config('app.contracts.PatientData'), [
                        'id' => null,
                        'card_identity' => [
                            'old_mr' => $data['no_emr'] ?? null,
                            'ihs_number' => $data['ihs_satu_sehat'] ?? null,
                            'bpjs' => $data['no_bpjs'] ?? null
                        ],
                        'name' => $data['nama'],
                        'reference_type' => "People",
                        'reference' => [
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'phone_1' => $data['kontakhp'] ?? null,
                            'phone_2' => null,
                            'blood_type' => $data['golongan_darah'] ?? null,
                            'pob' => $data['tempat_lahir'] ?? null,
                            'dob' => $data['tanggal_lahir'] ?? null,
                            'sex' => $data['jenis_kelamin'] ?? null,
                            'address' => [
                                'ktp' => [
                                    'name' => $data['alamat_ktp'] ?? null,
                                    'rt' => null,
                                    'rw' => null,
                                    'zip_code' => null
                                ],
                                'residence' => [
                                    'name' => $data['alamat_domisili'] ?? null,
                                    'rt' => null,
                                    'rw' => null,
                                    'zip_code' => null
                                ]
                            ],
                            'card_identity' => [
                                'nik' => $data['nik'] ?? null,
                                'nik_ibu' => null,
                                'sim' => null,
                                'passport' => $data['passport'] ?? null,
                                'kk' => null
                            ]
                        ]
                    ])
                );
            }
        }
    }

    protected function resolveFilePath(string $path): string
    {
        // 0️⃣ Absolute filesystem path (misal: /var/www/... atau C:\...)
        if (is_file($path)) {
            return $path;
        }

        // 1️⃣ Path relatif ke storage Laravel (default disk)
        $defaultDisk = Storage::disk(config('filesystems.default'));
        if ($defaultDisk->exists($path)) {
            return $defaultDisk->path($path);
        }

        // 2️⃣ Public storage URL di domain yang sama
        // contoh: https://domain.com/storage/import/file.xlsx
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $appUrl = rtrim(config('app.url'), '/');
            $url    = rtrim($path, '/');
            // ✅ Same domain
            if (Str::startsWith($url, $appUrl)) {
                $relativePath = Str::after($url, $appUrl);

                // /storage/xxx → storage/app/public/xxx
                if (Str::startsWith($relativePath, '/storage/')) {
                    $storagePath = Str::after($relativePath, '/storage/');
                    if (Storage::disk('public')->exists($storagePath)) {
                        return Storage::disk('public')->path($storagePath);
                    }
                }
            }

            // 3️⃣ External domain (S3 / CDN / domain lain)
            return $this->downloadToTemp($path);
        }

        // 4️⃣ Fallback: cek disk s3 langsung (path internal, bukan URL)
        if (Storage::disk('s3')->exists($path)) {
            return $this->copyDiskToTemp('s3', $path);
        }

        throw new \Exception("File tidak ditemukan atau tidak dapat diakses: {$path}");
    }

    protected function downloadToTemp(string $url): string{
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'tmp';
        $tempPath = sys_get_temp_dir() . '/import_' . uniqid() . '.' . $extension;
        $response = Http::timeout(30)->get($url);
        if (!$response->successful()) {
            throw new \Exception("Gagal download file dari URL: {$url}");
        }
        file_put_contents($tempPath, $response->body());
        return $tempPath;
    }

    protected function copyDiskToTemp(string $disk, string $path): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'import_');
        $content  = Storage::disk($disk)->get($path);
        file_put_contents($tempPath, $content);
        return $tempPath;
    }

}
