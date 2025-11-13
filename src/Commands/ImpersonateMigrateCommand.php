<?php

namespace Projects\WellmedBackbone\Commands;

use Hanafalah\MicroTenant\Commands\Impersonate\ImpersonateMigrateCommand as ImpersonateImpersonateMigrateCommand;

class ImpersonateMigrateCommand extends ImpersonateImpersonateMigrateCommand
{
    protected $signature = 'wellmed-backbone:impersonate-migrate 
                                {--app= : The type of the application}
                                {--group= : The type of the group}
                                {--tenant= : The type of the tenant}
                                {--app_id= : The id of the application}
                                {--group_id= : The id of the group}
                                {--tenant_id= : The id of the tenant}
                            ';

    protected function impersonateConfig(array $config_path) : self{
        foreach($config_path as $key => $config) {
            if(isset($config)) {
                $path         = $config->path.DIRECTORY_SEPARATOR.'wellmed-backbone'.'/src/'.$config['config']['generates']['config']['path'];
                $config       = $path.DIRECTORY_SEPARATOR.'config.php';
                if (is_file($config)){
                    $this->basePathResolver($config);
                    $config       = include($config);
                    $this->__impersonate[$key] = $config;
                }
            }
        }
        return $this;
    }
}