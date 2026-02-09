<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Imports\PatientImport;

class PatientImportJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable, SerializesModels, HasRequestData;

    public array $data;

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 3600; // 1 hour

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * The unique ID of the job - prevents duplicate processing of same file
     */
    public function uniqueId(): string
    {
        $paths = $this->data['support']['paths'] ?? [];
        return 'patient_import_' . md5(json_encode($paths) . ($this->data['tenant_id'] ?? ''));
    }

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 7200; // 2 hours for large imports

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Increase memory limit for large imports (18k+ rows)
        ini_set('memory_limit', '1G');

        $support = $this->data['support'] ?? [];
        $paths = $support['paths'] ?? [];

        // Use job's uniqueId as the importId for Redis tracking
        $importId = $this->uniqueId();

        Log::channel('import')->info('PatientImportJob started', [
            'tenant_id' => $this->data['tenant_id'] ?? null,
            'paths_count' => count($paths),
            'paths' => $paths,
            'import_id' => $importId,
        ]);

        MicroTenant::tenantImpersonate($this->data['tenant_id']);

        // Create single importer instance
        $importer = new PatientImport($importId);

        foreach ($paths as $index => $path) {
            try {
                Log::channel('import')->info('Processing file', [
                    'index' => $index,
                    'path' => $path,
                    'import_id' => $importId,
                ]);
                $filePath = $this->resolveFilePath($path);
            } catch (\Throwable $th) {
                Log::channel('import')->error('Failed to resolve file path', [
                    'path' => $path,
                    'error' => $th->getMessage(),
                ]);
                throw $th;
            }
            Excel::import($importer, $filePath);
        }

        Log::channel('import')->info('PatientImportJob completed', [
            'import_id' => $importId,
        ]);
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
