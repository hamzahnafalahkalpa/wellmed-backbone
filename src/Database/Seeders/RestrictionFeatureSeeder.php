<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class RestrictionFeatureSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $tenant = tenancy()->tenant;
        if ($tenant->product_type == 'LITE'){
            $this->call([
                LiteRestrictionSeeder::class
            ]);
        }
    }
}
