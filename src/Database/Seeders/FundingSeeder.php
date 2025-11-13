<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class FundingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $fundingModel = app(config('database.models.Funding'));

        $fundings = [
            ['name' => 'Mandiri'],
            ['name' => 'APBN'],
            ['name' => 'APBD'],
            ['name' => 'BLUD'],
            ['name' => 'Hibah'],
            ['name' => 'Lainnya'],
        ];

        foreach ($fundings as $funding) {
            $fundingModel::updateOrCreate([
                'name' => $funding['name'],
                'flag' => 'Funding'
            ]);
        }
    }
}
