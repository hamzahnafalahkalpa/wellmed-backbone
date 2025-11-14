<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder{
    public function run(){
        $provinces = include(__DIR__ . '/data/provinces.php');
        $province_model = app(config('database.models.Province'));
        foreach ($provinces as $province) {
            $province_model->updateOrCreate($province);
        }
    }
}