<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Hanafalah\WellmedFeature\Database\Seeders\DatabaseSeeder as MasterFeatureSeeder;
use Projects\WellmedBackbone\Jobs\JobRequest;

class AddDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        try {
            $this->call([
                AddNewTenantSeeder::class,                
                InstallerSeeder::class,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
