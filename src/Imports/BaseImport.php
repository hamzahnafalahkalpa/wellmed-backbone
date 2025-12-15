<?php

namespace Projects\WellmedBackbone\Imports;

use Hanafalah\LaravelSupport\Concerns\DatabaseConfiguration\HasModelConfiguration;
use Hanafalah\LaravelSupport\Concerns\ServiceProvider\HasConfiguration;
use Hanafalah\LaravelSupport\Concerns\Support\HasArray;
use Hanafalah\LaravelSupport\Concerns\Support\HasCall;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Projects\WellmedGateway\Concerns\HasUser;

class BaseImport {
    use HasConfiguration,
        HasModelConfiguration,
        HasCall, HasArray,
        HasRequest, HasUser;

    public function __construct(
        protected object $__schema
    ){
        $this->initConfig();
        $this->paramSetup();
    }
}