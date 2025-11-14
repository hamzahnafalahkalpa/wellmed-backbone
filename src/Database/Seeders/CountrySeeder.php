<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder{
    public function run(){
        $countries = include_once(__DIR__ . '/data/countries.php');
        $country_model = app(config('database.models.Country'));
        foreach ($countries as $country) {
            $country_model->updateOrCreate($country);
        }
    }
}