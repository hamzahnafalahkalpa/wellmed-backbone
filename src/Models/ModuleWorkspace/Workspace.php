<?php

namespace Projects\WellmedBackbone\Models\ModuleWorkspace;

use Hanafalah\ModuleWorkspace\Models\Workspace\Workspace as WorkspaceWorkspace;
use Projects\WellmedBackbone\Transformers\Workspace\SettingWorkspace;

class Workspace extends WorkspaceWorkspace
{
    public function getSettingResource(){
        return SettingWorkspace::class;
    }
}
