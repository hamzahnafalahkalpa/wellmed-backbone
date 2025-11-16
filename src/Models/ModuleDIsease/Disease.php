<?php

namespace Projects\WellmedBackbone\Models\ModuleDisease;

use Hanafalah\MicroTenant\Concerns\Models\HasTenantValidation;
use Hanafalah\ModuleDisease\Models\Disease as ModuleDiseaseDisease;
use Illuminate\Support\Str;

class Disease extends ModuleDiseaseDisease{
    use HasTenantValidation;

    public function whenTenantCreation(){
        if (!Str::contains($this->flag, 'Icd')) {
            if (!isset($this->tenant_id)) $this->tenant_id = \tenancy()->tenant->getKey();
        }
    }
}
