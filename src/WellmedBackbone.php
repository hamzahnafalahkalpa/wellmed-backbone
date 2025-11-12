<?php

namespace Projects\WellmedBackbone;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\{
    Concerns\Support\HasRepository,
    Supports\PackageManagement,
    Events as SupportEvents
};
use Projects\WellmedBackbone\Contracts\WellmedBackbone as ContractsWellmedBackbone;

class WellmedBackbone extends PackageManagement implements ContractsWellmedBackbone{
    use Supports\LocalPath,HasRepository;

    const LOWER_CLASS_NAME = "wellmed-backbone";
    const ID               = "1";

    public ?Model $model;

    public function events(){
        return [
            SupportEvents\InitializingEvent::class => [
                
            ],
            SupportEvents\EventInitialized::class  => [],
            SupportEvents\EndingEvent::class       => [],
            SupportEvents\EventEnded::class        => [],
            //ADD MORE EVENTS
        ];
    }

    protected function dir(): string{
        return __DIR__;
    }
}
