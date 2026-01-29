<?php

namespace Projects\WellmedBackbone\Commands;

use Hanafalah\LaravelSupport\Concerns\Support\HasArray;
use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Hanafalah\MicroTenant\Commands\Impersonate\Concerns\HasImpersonate;
use Hanafalah\MicroTenant\Commands\EnvironmentCommand;
use Hanafalah\MicroTenant\Facades\MicroTenant;

class GetElasticsearchIndexCommand extends EnvironmentCommand
{
    use HasCache, HasArray, HasImpersonate;

    protected $signature = 'wellmed-backbone:get-elasticsearch-index
                                {model : The model class to query (e.g., "Patient")}
                                {--limit=10 : Number of records to display}
                                {--from=0 : Offset from which to start}
                                {--search=* : Search filters in format field:value}
                                {--raw : Display raw Elasticsearch response}
                                {--app= : The type of the application}
                                {--group= : The type of the group}
                                {--tenant= : The type of the tenant}
                                {--app_id= : The id of the application}
                                {--group_id= : The id of the group}
                                {--tenant_id= : The id of the tenant}
                            ';

    protected $description = 'Get and display data from Elasticsearch index (multi-tenant)';

    public function handle(): void
    {
        $this->findApplication(function($project){
            $this->findGroup($project,function($group){
                $this->findTenant($group);
                $this->impersonateConfig([
                    "project"    => $this->__application,
                    "group"      => $this->__group,
                    "tenant"     => $this->__tenant
                ]);
                MicroTenant::tenantImpersonate($this->__tenant);

                // Display tenant information
                $this->info("Tenant: {$this->__tenant->name} (ID: {$this->__tenant->getKey()})");
                $this->info("Group: {$this->__group->name}");
                $this->info("Application: {$this->__application->name}");
                $this->newLine();

                // Call the base elasticsearch:get-index command with all options
                $this->call('elasticsearch:get-index', [
                    'model' => $this->argument('model'),
                    '--limit' => $this->option('limit'),
                    '--from' => $this->option('from'),
                    '--search' => $this->option('search'),
                    '--raw' => $this->option('raw')
                ]);
            });
        });
    }

    protected function impersonateConfig(array $config_path) : self{
        foreach($config_path as $key => $config) {
            if(isset($config)) {
                $this->__impersonate[$key] = config('wellmed-backbone');
            }
        }
        return $this;
    }
}
