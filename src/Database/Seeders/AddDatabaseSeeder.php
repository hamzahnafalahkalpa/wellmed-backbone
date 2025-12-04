<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
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
            config([
                'laravel-feature.is_validate_restriction' => false,
                'laravel-support.use_id_as_primary_validation_unicode' => false,
                'app.use_license_validation' => false,
                'app.is_seeding' => true
            ]);
            $this->call([
                AddNewTenantSeeder::class,                
                InstallerSeeder::class,
                UserAdminSeeder::class,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
