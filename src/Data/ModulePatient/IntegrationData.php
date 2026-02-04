<?php

namespace Projects\WellmedBackbone\Data\ModulePatient;

use Hanafalah\LaravelSupport\Supports\Data;
use Projects\WellmedBackbone\Contracts\Data\ModulePatient\IntegrationData as ContractsIntegrationData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class IntegrationData extends Data implements ContractsIntegrationData{
    #[MapInputName('satu_sehat')]
    #[MapName('satu_sehat')]
    public ?IntegrationTemplateData $satu_sehat = null;

    #[MapInputName('bpjs')]
    #[MapName('bpjs')]
    public ?IntegrationTemplateData $bpjs = null;

    public static function before(array &$attributes){
        $attributes['satu_sehat'] ??= [
            'flag' => 'satu-sehat',
            'label' => 'Satu Sehat',
            'progress' => 0,
            'pending' => 0,
            'syncs' => [
                ['label' => 'Kunjungan','flag' => 'encounter','progress' => 0],
                ['label' => 'Resep','flag' => 'dispense','progress' => 0],
                ['label' => 'Diagnosa','flag' => 'condition','progress' => 0]
            ],
            'logs' => []
        ];
        $attributes['bpjs'] ??= [
            'flag' => 'bpjs',
            'label' => 'Bpjs',
            'progress' => 0,
            'pending' => 0,
            'syncs' => [
                ['label' => 'Kunjungan','flag' => 'encounter','progress' => 0],
                ['label' => 'Resep','flag' => 'dispense','progress' => 0],
                ['label' => 'Diagnosa','flag' => 'condition','progress' => 0]
            ],
            'logs' => []
        ];
    }
}