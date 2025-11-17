<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Contracts\Data\TenantData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Hanafalah\WellmedPlusStarterpack\Concerns\HasComposer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Jobs\JobRequest;

class AddNewTenantSeeder extends Seeder{
    use HasRequestData, HasComposer;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $tenantName = $data['tenant_name'] ?? 'default';
        $workspace_id = $data['workspace_id'];
        $workspace_name = $data['workspace_name'];

        $group_tenant_id = $data['group_tenant_id'];
        $group_tenant = app(config('database.models.Tenant'))->findOrFail($group_tenant_id);
        $tenant_namespace = Str::studly($group_tenant->name);

        $app_tenant_id = $data['app_tenant_id'];
        $app_tenant = app(config('database.models.Tenant'))->findOrFail($app_tenant_id);

        $tenant_schema  = app(config('app.contracts.Tenant'));
        $tenant_model   = app(config('database.models.Tenant'));
        $generator_config = config('laravel-package-generator');
        $studlyname = Str::studly($workspace_name);
        $tenant = $tenant_schema->prepareStoreTenant($this->requestDTO(TenantData::class,[
            'parent_id'      => $group_tenant_id,
            'name'           => $workspace_name,
            'flag'           => $tenant_model::FLAG_TENANT,
            'reference_id'   => $workspace_id,
            'reference_type' => 'Workspace',
            'domain'         => [
                'domain' => 'localhost:9000'
            ],
            'provider' => $tenant_namespace.'\\Tenant'.$studlyname.'\\Providers\\Tenant'.$studlyname.'ServiceProvider',
            'path'     => $generator_config['patterns']['tenant']['published_at'],
            'app'      => ['provider' => $app_tenant->provider],
            'group'    => ['provider' => $group_tenant->provider],
            'packages' => [],
            'product_type'     => request()->product_label,
            'is_recurring'     => true,
            'recurring_period' => 'MONTHLY',
            'started_at' => now(),
            'expired_at' => now()->addYear(),
            'config'   => $generator_config['patterns']['tenant']
        ]));
        $tenant->db_name = $tenant->tenancy_db_name;
        $tenant->save();

        // Artisan::call('impersonate:cache',[
        //     '--forget'    => true,
        // ]);
        Artisan::call('impersonate:cache',[
            '--app_id'    => $app_tenant->getKey(),
            '--group_id'  => $group_tenant->getKey(),
            '--tenant_id' => $tenant->getKey()
        ]);
        Artisan::call('wellmed-backbone:impersonate-migrate',[
            '--app'       => true,
            '--app_id'    => $app_tenant->getKey(),
            '--group_id'  => $group_tenant->getKey(),
            '--tenant_id' => $tenant->getKey()
        ]);

        MicroTenant::tenantImpersonate($tenant);
    }
}