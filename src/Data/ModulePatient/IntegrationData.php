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
                [
                    'flag' => 'patient',
                    'label' => 'Pasien',
                    'from' => 0,
                    'to' => 0,
                    'progress' => 0,
                    'last_updated_at' => null
                ],
                [
                    'flag' => 'encounter',
                    'label' => 'Kunjungan',
                    'from' => 0,
                    'to' => 0,
                    'progress' => 0,
                    'last_updated_at' => null
                ],
                [
                    'flag' => 'observation',
                    'label' => 'Observasi',
                    'from' => 0,
                    'to' => 0,
                    'progress' => 0,
                    'last_updated_at' => null
                ]
            ],
            'logs' => []
        ];
        $attributes['bpjs'] ??= [
            'flag' => 'bpjs',
            'label' => 'Bpjs',
            'progress' => 0,
            'pending' => 0,
            'syncs' => [
                [
                    'flag' => 'encounter',
                    'label' => 'Kunjungan',
                    'from' => 0,
                    'to' => 0,
                    'progress' => 0,
                    'last_updated_at' => null
                ],
                [
                    'flag' => 'observation',
                    'label' => 'Observasi',
                    'from' => 0,
                    'to' => 0,
                    'progress' => 0,
                    'last_updated_at' => null
                ]
            ],
            'logs' => []
        ];
    }
}