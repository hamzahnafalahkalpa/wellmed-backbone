<?php

namespace Projects\WellmedBackbone\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class PatientImportJob implements ShouldQueue
{
    use Queueable, SerializesModels;

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
        $paths = $this->data['paths'] ?? [];

        foreach ($paths as $path) {
            $filePath = $this->resolveFilePath($path);

            $data = Excel::toArray(
                new class implements \Maatwebsite\Excel\Concerns\ToArray, 
                             \Maatwebsite\Excel\Concerns\WithStartRow {
                    public function startRow(): int {
                        return 2;
                    }
                    public function array(array $array) {}
                },
                $filePath
            )[0];

            // Proses data sesuai kebutuhan
            dd($data);
        }
    }

    /**
     * Resolve path from local disk, Laravel disk (S3), or URL
     */
    protected function resolveFilePath(string $path): string
    {
        // 1️⃣ Disk default Laravel
        $disk = Storage::disk(config('filesystems.default'));
        if ($disk->exists($path)) {
            return $disk->path($path);
        }

        // 2️⃣ URL publik (HTTP/HTTPS)
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $tempPath = tempnam(sys_get_temp_dir(), 'import_');
            $response = Http::get($path);

            if (!$response->ok()) {
                throw new \Exception("Gagal download file dari URL: {$path}");
            }

            file_put_contents($tempPath, $response->body());
            return $tempPath;
        }

        // 3️⃣ Disk S3 Laravel (jika disk default bukan S3 dan file di S3)
        if (Storage::disk('s3')->exists($path)) {
            $tempPath = tempnam(sys_get_temp_dir(), 'import_');
            $content = Storage::disk('s3')->get($path);
            file_put_contents($tempPath, $content);
            return $tempPath;
        }

        throw new \Exception("File tidak ditemukan: {$path}");
    }
}
