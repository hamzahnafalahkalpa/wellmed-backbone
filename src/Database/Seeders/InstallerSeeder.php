<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;

class InstallerSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        try {
            $this->call([
                PermissionSeeder::class,
                RoleSeeder::class,
                // MasterFeatureSeeder::class,
                EncodingSeeder::class,
                MasterSeeder::class,
                AssetSeeder::class,
                MasterReportCollectionSeeder::class,
                RestrictionFeatureSeeder::class
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
