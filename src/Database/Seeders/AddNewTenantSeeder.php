<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Contracts\Data\TenantData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Hanafalah\WellmedPlusStarterpack\Concerns\HasComposer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Hanafalah\LaravelSupport\Jobs\JobRequest;
use Illuminate\Support\Facades\Log;

class AddNewTenantSeeder extends Seeder{
    use HasRequestData, HasComposer;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $tenantName     = $data['tenant_name'] ?? 'default';
        $workspace_id   = $data['workspace_id'];
        $workspace_model = app(config('database.models.Workspace'))->findOrFail($workspace_id);
        $workspace_name = $data['workspace_name'];

        $group_tenant_id  = $data['group_tenant_id'];
        $group_tenant     = app(config('database.models.Tenant'))->findOrFail($group_tenant_id);
        $tenant_namespace = Str::studly($group_tenant->name);

        $app_tenant_id = $data['app_tenant_id'];
        $app_tenant    = app(config('database.models.Tenant'))->findOrFail($app_tenant_id);

        $tenant_schema    = app(config('app.contracts.Tenant'));
        $tenant_model     = app(config('database.models.Tenant'));
        $generator_config = config('laravel-package-generator');
        $studlyname       = Str::studly($workspace_name);
        $tenant = $tenant_schema->prepareStoreTenant($this->requestDTO(TenantData::class,[
            'parent_id'      => $group_tenant_id,
            'name'           => $workspace_name,
            'flag'           => $tenant_model::FLAG_TENANT,
            'reference_id'   => (string) $workspace_id,
            'reference_type' => 'Workspace',
            'domain'         => [
                'domain' => 'localhost:9000'
            ],
            'provider' => $tenant_namespace.'\\Tenant'.$studlyname.'\\Providers\\Tenant'.$studlyname.'ServiceProvider',
            'path'     => $generator_config['patterns']['tenant']['published_at'],
            'app'      => ['provider' => $app_tenant->provider],
            'group'    => ['provider' => $group_tenant->provider],
            'packages' => [],
            'product_type'     => $data['product_label'],
            'is_recurring'     => true,
            'recurring_period' => 'MONTHLY',
            'started_at' => now(),
            'expired_at' => now()->addYear(),
            'config'   => $generator_config['patterns']['tenant']
        ]));
        $tenant->db_name = $tenant->tenancy_db_name;
        $tenant->save();

        // $tenant = $tenant_model->find(8);

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

        try {
            $form_payload = [
                "active" => true,
                "organization_code" => config('satu-sehat.organization_id'),
                "organization_name" => $workspace_name,
                "part_of_organization_code" => config('satu-sehat.organization_id'),
                "type" => [
                    "dept" => "KLINIK"
                ],
                "address" => [
                    "work" => [
                        "name" => $workspace_model->setting['address']['name'] ?? 'Unknown Address',
                        "city" => "Jakarta",
                        "postal_code" => "11290",
                        "province_code" => "31",
                        "city_code" => "3171",
                        "district_code" => "317107",
                        "village_code" => "3171071004",
                        "rw" => "4",
                        "rt" => "50"
                    ]
                ],
                "telecom" => [
                    "work"=> [
                        "phone" => ["0811######"],
                    //  "email" => ["a@mail.com"]
                     ]
                ]
            ];
            $organization_satu_sehat = app(config('app.contracts.OrganizationSatuSehat'))->useAccessToSatuSehat()
                ->prepareStoreOrganizationSatuSehat(
                $this->requestDTO(
                    config('app.contracts.OrganizationSatuSehatData'),[
                        'model' => $workspace_model,
                        'form'  => $form_payload
                    ]
                )
            );
            $integration = $workspace_model->integration ?? [];
            $integration['satu_sehat']['general']['ihs_number'] = $organization_satu_sehat->response['id'] ?? null;
            $workspace_model->integration = $integration;
            $workspace_model->save();
        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error($th->getMessage());
        }
    }
}