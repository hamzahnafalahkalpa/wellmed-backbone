<?php

namespace Projects\WellmedBackbone\Models\ModuleIcd;

use Hanafalah\ModuleIcd\Resources\Icd10\ViewIcd10;

class Icd10 extends Icd
{
    protected $table = 'diseases';

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('icd_10', function ($query) {
            $query->whereLike('version', 'Icd10');
        });
    }

    public function getViewResource(){return ViewIcd10::class;}
    public function getShowResource(){return ViewIcd10::class;}
}
