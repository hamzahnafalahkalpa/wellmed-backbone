<?php

namespace Projects\WellmedBackbone\Models\ModuleWorkspace;

use Hanafalah\ModuleLicense\Concerns\HasLicense;
use Hanafalah\ModuleLicense\Concerns\HasModelHasLicense;
use Hanafalah\ModuleWorkspace\Models\Workspace\Workspace as WorkspaceWorkspace;
use Projects\WellmedBackbone\Transformers\Workspace\SettingWorkspace;

class Workspace extends WorkspaceWorkspace
{
    use HasLicense, HasModelHasLicense;

    public function getSettingResource(){
        return SettingWorkspace::class;
    }

    public function installedFeatures(){
        return $this->morphManyModel('InstalledFeature','model');
    }
}
