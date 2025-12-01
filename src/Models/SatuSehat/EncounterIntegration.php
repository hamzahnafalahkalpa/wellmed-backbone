<?php

namespace Projects\WellmedBackbone\Models\SatuSehat;

use Hanafalah\SatuSehat\Models\SatuSehatLog;

class EncounterIntegration extends SatuSehatLog
{
    protected $table = 'satu_sehat_logs';
    
    protected static function booted(): void{
        parent::booted();
        static::addGlobalScope('name',function($query){
            $query->where('name','EncounterSatuSehat');
        });
    }
}
