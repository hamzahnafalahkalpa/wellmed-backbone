<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Hanafalah\WellmedFeature\Database\Seeders\DatabaseSeeder as MasterFeatureSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            $this->call([
                WorkspaceSeeder::class,
                ApiAccessSeeder::class,
                InstallerSeeder::class,
                EmployeeSeeder::class,
                IcdSeeder::class
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
