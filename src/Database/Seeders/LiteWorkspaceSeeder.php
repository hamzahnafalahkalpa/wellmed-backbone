<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Contracts\Data\TenantData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Hanafalah\ModuleWorkspace\Enums\Workspace\Status;
use Hanafalah\WellmedLiteStarterpack\Concerns\HasComposer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Projects\WellmedBackbone\Jobs\JobRequest;

class LiteWorkspaceSeeder extends Seeder{
    use HasRequestData, HasComposer;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        request()->merge([
            'workspace_uuid' => '9e7ff0f6-7679-46c8-ac3e-71da818160sf'
        ]);
        $workspace = app(config('database.models.Workspace'))->uuid('9e7ff0f6-7679-46c8-ac3e-71da818160sf')->first();        
        $generator_config = config('laravel-package-generator');
        $project_namespace = 'Projects';
        $group_namespace   = 'WellmedLite';
        if (!isset($workspace)){
            $tenant_namespace  = 'WellmedLiteGroup';

            $db_tenant_name = config('micro-tenant.database.database_tenant_name');
            config([
                'tenancy.database.prefix' => env('BACKBONE_DATABASE_PREFIX', 'app_'),
                'tenancy.database.suffix' => '',
            ]);

            $tenant_schema  = app(config('app.contracts.Tenant'));
            $tenant_model   = app(config('database.models.Tenant'));
            $project_tenant = $tenant_schema->prepareStoreTenant($this->requestDTO(TenantData::class,[
                'parent_id'      => null,
                'name'           => 'Wellmed Lite',
                'flag'           => $tenant_model::FLAG_APP_TENANT,
                'reference_id'   => null,
                'reference_type' => null,
                'provider'       => $project_namespace.'\\WellmedLite\\Providers\\WellmedLiteServiceProvider',
                'path'           => $generator_config['patterns']['project']['published_at'],
                'packages'       => [],
                'has_group'      => true,
                'has_tenant'     => true,
                'product_type'   => 'LITE',
                'config'         => $generator_config['patterns']['project']
            ]));

            $database_tenant_name = config('micro-tenant.database.central_tenant');
            config([
                'tenancy.database.prefix' => $database_tenant_name['prefix'],
                'tenancy.database.suffix' => $database_tenant_name['suffix'],
                // 'tenancy.database.central_connection' => 'central_tenant'
            ]);

            $group_tenant = $tenant_schema->prepareStoreTenant($this->requestDTO(TenantData::class,[
                'parent_id'      => $project_tenant->getKey(),
                'name'           => 'Wellmed Lite',
                'flag'           => $tenant_model::FLAG_CENTRAL_TENANT,
                'reference_id'   => null,
                'reference_type' => null,
                'provider'       => $group_namespace.'\\GroupInitialWellmedLite\\Providers\\GroupInitialWellmedLiteServiceProvider',
                'app'            => ['provider' => $project_tenant->provider],
                'path'           => $generator_config['patterns']['group']['published_at'],
                'has_tenant'     => true,
                'product_type'   => 'LITE',
                'packages'       => [],
                'config'         => $generator_config['patterns']['group']
            ]));
            config([
                'tenancy.database.prefix' => $db_tenant_name['prefix'],
                'tenancy.database.suffix' => $db_tenant_name['suffix'],
                // 'tenancy.database.central_connection' => $default
            ]);

            $timezone = app(config('database.models.Timezone'))->where('label','WIB')->first();
            $workspace = app(config('app.contracts.Workspace'))->prepareStoreWorkspace($this->requestDTO(
                config('app.contracts.WorkspaceData'),[
                    'uuid'    => '9e7ff0f6-7679-46c8-ac3e-71da818160sf',
                    'name'    => 'Wellmed Lite',
                    'status'  => Status::ACTIVE->value,
                    'product_type'   => 'LITE',
                    'setting' => [
                        'timezone_id' => $timezone->getKey(),
                        'timezone' => $timezone->toViewApi()->resolve(),
                        'address' => [
                            'name'           => 'sangkuriang',
                            'province_id'    => null,
                            'district_id'    => null,
                            'subdistrict_id' => null,
                            'village_id'     => null
                        ],
                        'email'   => 'hamzahnuralfalah_lite@gmail.com',
                        'phone'   => '081906521808',
                        'owner_id' => null,
                        'owner' => [
                            'id' => null,
                            'name' => null
                        ]
                    ],
                    'integration' => [
                        "satu_sehat" => [
                            "progress" => 0,
                            "general" => [
                                "ihs_number" => null
                            ],
                            "syncs" => [
                                [
                                    'flag' => 'encounter',
                                    'label' => 'Kunjungan',
                                ],
                                [
                                    'flag' => 'condition',
                                    'label' => 'Diagnosa',
                                ], 
                                [
                                    'flag' => 'dispense',
                                    'label' => 'Resep',
                                ]
                            ]
                        ],
                        "bpjs" => [
                            "progress" => 0,
                            "syncs" => [
                                [
                                    'flag' => 'encounter',
                                    'label' => 'Kunjungan',
                                ],
                                [
                                    'flag' => 'condition',
                                    'label' => 'Diagnosa',
                                ], 
                                [
                                    'flag' => 'dispense',
                                    'label' => 'Resep',
                                ]
                            ]
                        ]
                    ],
                    
                ]
            ));

            $tenant = $tenant_schema->prepareStoreTenant($this->requestDTO(TenantData::class,[
                'parent_id'      => $group_tenant->getKey(),
                'name'           => 'Tenant Wellmed Lite',
                'flag'           => $tenant_model::FLAG_TENANT,
                'reference_id'   => $workspace->getKey(),
                'reference_type' => $workspace->getMorphClass(),
                'domain'         => [
                    'domain' => 'localhost:9000'
                ],
                'provider' => $tenant_namespace.'\\TenantWellmedLite\\Providers\\TenantWellmedLiteServiceProvider',
                'path'     => $generator_config['patterns']['tenant']['published_at'],
                'app'      => ['provider' => $project_tenant->provider],
                'group'    => ['provider' => $group_tenant->provider],
                'packages' => [],
                'product_type'     => 'LITE',
                'is_recurring'     => true,
                'recurring_period' => 'MONTHLY',
                'started_at' => now(),
                'expired_at' => now()->addYear(),
                'config'   => $generator_config['patterns']['tenant']
            ]));
            $tenant->db_name = $tenant->tenancy_db_name;
            $tenant->save();
        }else{
            $tenant         = $workspace->tenant;
            $group_tenant   = $tenant->parent;
            $project_tenant = $group_tenant->parent;
        }
        request()->merge([
            'workspace_id' => $workspace->getKey()
        ]);
        Artisan::call('impersonate:cache',[
            '--app_id'    => $project_tenant->getKey(),
            '--group_id'  => $group_tenant->getKey(),
            '--tenant_id' => $tenant->getKey()
        ]);

        Artisan::call('wellmed-backbone:impersonate-migrate',[
            '--app'       => true,
            '--app_id'    => $project_tenant->getKey(),
            '--group_id'  => $group_tenant->getKey(),
            '--tenant_id' => $tenant->getKey()
        ]);

        MicroTenant::tenantImpersonate($tenant);
        JobRequest::set([
            'workspace_id' => $workspace->getKey()
        ]);
    }
}