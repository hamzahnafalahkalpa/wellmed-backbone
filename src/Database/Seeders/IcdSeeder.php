<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class IcdSeeder extends Seeder
{
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $sqlFile = __DIR__ . '/data/diseases.sql';
                
        if (File::exists($sqlFile)) {
            $sql = File::get($sqlFile);
            $disease = app(config('database.models.Disease'))->first();
            if (!$disease) {
                \DB::connection('central')->unprepared($sql);
            }
        }
    }
}
