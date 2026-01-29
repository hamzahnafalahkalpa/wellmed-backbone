<?php

namespace Projects\WellmedBackbone\Commands;

use Hanafalah\LaravelSupport\Concerns\Support\HasArray;
use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Hanafalah\MicroTenant\Commands\Impersonate\Concerns\HasImpersonate;
use Hanafalah\MicroTenant\Commands\EnvironmentCommand;
use Hanafalah\MicroTenant\Facades\MicroTenant;

class DeleteElasticsearchIndexCommand extends EnvironmentCommand
{
    use HasCache, HasArray, HasImpersonate;

    protected $signature = 'wellmed-backbone:delete-elasticsearch-index
                                {model : The model class (e.g., "Patient")}
                                {--flush : Only delete all documents, keep the index structure}
                                {--force : Force deletion without confirmation}
                                {--app= : The type of the application}
                                {--group= : The type of the group}
                                {--tenant= : The type of the tenant}
                                {--app_id= : The id of the application}
                                {--group_id= : The id of the group}
                                {--tenant_id= : The id of the tenant}
                            ';

    protected $description = 'Delete Elasticsearch index or flush documents (multi-tenant)';

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
                $this->warn("⚠️  CAUTION: You are about to modify Elasticsearch index for:");
                $this->info("Tenant: {$this->__tenant->name} (ID: {$this->__tenant->getKey()})");
                $this->info("Group: {$this->__group->name}");
                $this->info("Application: {$this->__application->name}");
                $this->newLine();

                // Call the base elasticsearch:delete-index command with all options
                $this->call('elasticsearch:delete-index', [
                    'model' => $this->argument('model'),
                    '--flush' => $this->option('flush'),
                    '--force' => $this->option('force')
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
