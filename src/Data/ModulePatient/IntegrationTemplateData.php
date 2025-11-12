<?php

namespace Projects\WellmedBackbone\Data\ModulePatient;

use Hanafalah\LaravelSupport\Supports\Data;
use Projects\WellmedBackbone\Contracts\Data\ModulePatient\IntegrationTemplateData as ContractsIntegrationTemplateData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class IntegrationTemplateData extends Data implements ContractsIntegrationTemplateData{
    #[MapInputName('flag')]
    #[MapName('flag')]
    public ?string $flag = null;

    #[MapInputName('label')]
    #[MapName('label')]
    public ?string $label = null;

    #[MapInputName('progress')]
    #[MapName('progress')]
    public ?float $progress = 0;

    #[MapInputName('last_updated_at')]
    #[MapName('last_updated_at')]
    public ?string $last_updated_at = null;

    #[MapInputName('from')]
    #[MapName('from')]
    public ?int $from = 0;

    #[MapInputName('to')]
    #[MapName('to')]
    public ?int $to = 0;

    #[MapInputName('syncs')]
    #[MapName('syncs')]
    #[DataCollectionOf(IntegrationTemplateData::class)]
    public ?array $syncs = [];

    public static function before(array &$attributes){
        $attributes['progress'] ??= 0;
        $attributes['last_updated_at'] ??= now()->toDateTimeString();
        $attributes['from'] ??= 0;
        $attributes['to'] ??= 0;
    }
}