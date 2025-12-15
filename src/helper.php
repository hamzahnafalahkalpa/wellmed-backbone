<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('backbone_asset')) {
    /**
     * Generate asset URL, compatible with local/public and S3.
     */
    function backbone_asset(string $url): string
    {
        $disk = config('filesystems.default', 'public');
        $base = rtrim(config('filesystems.asset_url', '/assets'), '/');
        $path = ltrim($url, '/');
        // Kalau disk-nya S3, generate via Storage
        if ($disk === 's3') {
            return Storage::disk('s3')->url($path);
        }

        // Selain S3, tetap pakai asset() lokal
        return asset($base . '/' . $path);
    }
}