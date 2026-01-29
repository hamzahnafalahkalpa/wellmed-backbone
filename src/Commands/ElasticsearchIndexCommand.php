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
                                {--app= : The type of the application}
                                {--group= : The type of the group}
                                {--tenant= : The type of the tenant}
                                {--app_id= : The id of the application}
                                {--group_id= : The id of the group}
                                {--tenant_id= : The id of the tenant}
                            ';
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