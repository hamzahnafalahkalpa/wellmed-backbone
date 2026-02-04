<?php

namespace Projects\WellmedBackbone\Models\ModuleUser;

use Hanafalah\LaravelHasProps\Concerns\HasCurrent;
use Hanafalah\ModuleLicense\Concerns\HasModelHasLicense;
use Hanafalah\ModuleUser\Models\User\UserReference as UserUserReference;

class UserReference extends UserUserReference{
    use HasModelHasLicense, HasCurrent;

    protected $list = [
        'id','uuid','reference_type','reference_id',
        'user_id','workspace_type','workspace_id','current',
        'flag'
    ];

    public function getConditions(): array{
        return ['reference_type', 'reference_id', 'user_id', 'flag'];
    }

    protected static function booted(): void{
        parent::booted();
        static::creating(function($query){
            $query->flag = 'WELLMED';
        });
        // static::addGlobalScope('license',function($query){
        //     if (config('app.use_license_validation', true)){
        //         $query->whereHas('modelHasLicense');
        //     }
        // });
    }

    public function tenant(){return $this->morphTo(__FUNCTION__, "workspace_type", "workspace_id");}

}
