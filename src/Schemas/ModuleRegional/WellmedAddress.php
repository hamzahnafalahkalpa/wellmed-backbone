<?php

namespace Projects\WellmedBackbone\Schemas\ModuleWorkspace;

use Hanafalah\ModuleRegional\Schemas\Regional\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleRegional\WellmedAddress as ModuleRegionalWellmedAddress;

class WellmedAddress extends Address implements ModuleRegionalWellmedAddress
{
    protected string $__entity = 'WellmedAddress';
    public $wellmed_address_model;
}
