<?php

namespace Projects\WellmedBackbone\Models\ModuleIcd;

use Hanafalah\ModuleIcd\Resources\Icd\ViewIcd;
use Projects\WellmedBackbone\Models\ModuleDisease\Disease;

class Icd extends Disease
{
    protected $table = 'diseases';

    protected $casts = [
        'name' => 'string',
        'code' => 'string'
    ];

    public function getViewResource(){return ViewIcd::class;}
    public function getShowResource(){return ViewIcd::class;}
}
