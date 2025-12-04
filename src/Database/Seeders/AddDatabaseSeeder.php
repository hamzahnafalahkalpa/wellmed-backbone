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
        // JobRequest::set([
        //     'workspace_id'    => $workspace->getKey(),
        //     'workspace_name'  => $workspace->name,
        //     'product_label'   => $product_model->label,
        //     'app_tenant_id'   => $app_tenant->getKey(),
        //     'group_tenant_id' => $group_tenant->getKey(),
        //     'admin'           => $workspace->admin
        // ]);      

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
                RestrictionFeatureSeeder::class
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
