<?php

namespace Projects\WellmedBackbone\Transformers\Workspace;

use Hanafalah\ModuleWorkspace\Resources\Workspace\SettingWorkspace as WorkspaceSettingWorkspace;

class SettingWorkspace extends WorkspaceSettingWorkspace
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            "registration"  => [
                "is_examination"=> $this['registration']['is_examination'] ?? true,
                "direct_pos"=> $this['registration']['direct_pos'] ?? true
            ]
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
