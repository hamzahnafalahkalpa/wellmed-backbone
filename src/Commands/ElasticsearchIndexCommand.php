<?php

namespace Projects\WellmedBackbone\Commands;

use Hanafalah\MicroTenant\Commands\Impersonate\ElasticsearchIndexCommand as ImpersonateElasticsearchIndexCommand;
use Hanafalah\LaravelSupport\Concerns\Support\HasArray;
use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Hanafalah\MicroTenant\Commands\Impersonate\Concerns\HasImpersonate;
use Hanafalah\MicroTenant\Commands\EnvironmentCommand;
use Hanafalah\MicroTenant\Facades\MicroTenant;

class ElasticsearchIndexCommand extends EnvironmentCommand
{
    use HasCache, HasArray, HasImpersonate;

    protected $signature = 'wellmed-backbone:elasticsearch-index
                                {model}
                                {--chunk=100 : Chunk}
                                {--from=0 : Start from ?}
                                {--limit=0 : The limitation}
                                {--reindex : Flush existing data and reindex from scratch}
                                {--app= : The type of the application}
                                {--group= : The type of the group}
                                {--tenant= : The type of the tenant}
                                {--app_id= : The id of the application}
                                {--group_id= : The id of the group}
                                {--tenant_id= : The id of the tenant}
                            ';
    protected $description = 'Index Elasticsearch documents with optional reindexing (multi-tenant)';

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

                // Handle reindex: flush existing data first
                if ($this->option('reindex')) {
                    $this->warn('🔄 Reindex mode: Flushing existing data first...');
                    $this->newLine();

                    $this->call('elasticsearch:delete-index', [
                        'model' => $this->argument('model'),
                        '--flush' => true,
                        '--force' => true
                    ]);

                    $this->newLine();
                    $this->info('✅ Existing data flushed. Starting fresh indexing...');
                    $this->newLine();
                }

                $this->call('elasticsearch:index',[
                    'model' => $this->argument('model'),
                    '--chunk' => $this->option('chunk'),
                    '--from' => $this->option('from'),
                    '--limit' => $this->option('limit')
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