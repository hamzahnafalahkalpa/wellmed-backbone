<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AssetSeeder extends Seeder
{
    public function run()
    {
        $asset_path = __DIR__ . '/data/images';
        $disk = Storage::disk(config('filesystems.default'));
        $is_s3 = config('filesystems.default') === 's3';

        // Gunakan folder assets, baik untuk local maupun s3
        $target_dir = 'assets';

        $files = File::files($asset_path);

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $target_path = $target_dir . '/' . $filename;

            // Baca konten file dari local source
            $content = File::get($file->getRealPath());

            // Cek apakah file sudah ada dan apakah isinya sama
            if ($disk->exists($target_path)) {
                $existing_content = $disk->get($target_path);

                // Bandingkan isi file — kalau beda, overwrite
                if (md5($existing_content) !== md5($content)) {
                    $disk->put($target_path, $content);
                    $this->command->info("Updated changed file: {$target_path}");
                } else {
                    $this->command->info("Skipped identical file: {$target_path}");
                }
            } else {
                // Belum ada, simpan baru
                $disk->put($target_path, $content);
                $this->command->info("Uploaded new file: {$target_path}");
            }
        }
    }
}
