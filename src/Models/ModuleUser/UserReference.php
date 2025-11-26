<?php

namespace Projects\WellmedBackbone\Models\ModuleUser;

use Hanafalah\LaravelHasProps\Concerns\HasCurrent;
use Hanafalah\ModuleLicense\Concerns\HasModelHasLicense;
use Hanafalah\ModuleUser\Models\User\UserReference as UserUserReference;

class UserReference extends UserUserReference{
    use HasModelHasLicense,HasCurrent;

    protected static function booted(): void{
        parent::booted();
        static::addGlobalScope('license',function($query){
            $query->whereHas('modelHasLicense');
        });
    }

    public function tenant(){return $this->morphTo(__FUNCTION__, "workspace_type", "workspace_id");}

}
