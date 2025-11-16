<?php

namespace Projects\WellmedBackbone\Models\ModulePermission;

use Hanafalah\LaravelFeature\Concerns\HasRestrictionFeature;
use Hanafalah\LaravelPermission\Models\Permission\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    use HasRestrictionFeature;

    public function usedRestrictionAs(): array{
        return ['model'];
    }

}
