<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'TBContactHistory',
    'name'  => 'PEMERIKSAAN RIWAYAT KONTAK TBC',
    'dynamic_forms'  => [
        [
            'label'          => 'Apakah Pasien memiliki Riwayat Kontak TBC?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 'Ya'
                ],
                [
                    'label' => 'Tidak',
                    'value' => 'Tidak'
                ]
            ]
        ],
        [
            'label'          => 'Sebutkan Jenis Kontak TBC',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'TBC Paru Bakteriologis',
                    'value' => 'TBC Paru Bakteriologis'
                ],
                [
                    'label' => 'Klinis',
                    'value' => 'Klinis'
                ],
                [
                    'label' => 'Ekstra Paru',
                    'value' => 'Ekstra Paru'
                ]
            ]
        ]
    ]
];
