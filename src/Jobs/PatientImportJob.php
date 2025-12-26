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
use Projects\WellmedBackbone\Imports\PatientImport;

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
            try {
                $filePath = $this->resolveFilePath($path);
            } catch (\Throwable $th) {
                throw $th;
            }
            Excel::import(new PatientImport, $filePath);
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
