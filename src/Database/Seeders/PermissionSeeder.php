<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelPermission\Facades\LaravelPermission;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $permissions = LaravelPermission::scanPermissions(__DIR__.'/data/permissions');
        app(config('app.contracts.Permission'))->prepareStorePermission($permissions);
    }
}
