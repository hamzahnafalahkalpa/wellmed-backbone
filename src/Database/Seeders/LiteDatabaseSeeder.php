<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Hanafalah\WellmedFeature\Database\Seeders\DatabaseSeeder as MasterFeatureSeeder;

class LiteDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        try {
            $this->call([
                LiteWorkspaceSeeder::class,
                ApiAccessSeeder::class,
                InstallerSeeder::class,
                LiteEmployeeSeeder::class,
                IcdSeeder::class,
                ElasticSeeder::class
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
