<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegionalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $files = ['Country','Province', 'District', 'Subdistrict', 'Village'];
        foreach ($files as $file) {
            $model = app(config('database.models.'.$file));
            $first = $model->first();
            if (!isset($first)){
                $sql = file_get_contents(__DIR__ . '/data/' . Str::lower(Str::plural($file)) . '.sql');
                DB::unprepared($sql);
            }
        }
    }
}
