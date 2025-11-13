<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'HearingFunction',
    'name'  => 'PEMERIKSAAN FUNGSI PENDENGARAN',
    'dynamic_forms'  => [
        [
            'label'          => 'Tes berbisik Modifikasi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Normal (4-6 meter)',
                    'value' => 'Normal (4-6 meter)'
                ],
                [
                    'label' => 'Tuli Ringan (2 - <4 meter)',
                    'value' => 'Tuli Ringan (2 - <4 meter)'
                ],
                [
                    'label' => 'Tuli sedang (1- <2meter)',
                    'value' => 'Tuli sedang (1- <2meter)'
                ],
                [
                    'label' => 'Tuli berat (<10cm)',
                    'value' => 'Tuli berat (<10cm)'
                ],
                [
                    'label' => 'Tuli Total 0',
                    'value' => 'Tuli Total 0'
                ]
            ]
        ],
        [
            'label'          => 'Tes Penala Rinne',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Rinne (+)',
                    'value' => 'Rinne (+)'
                ],
                [
                    'label' => 'Rinne (-)',
                    'value' => 'Rinne (-)'
                ],
                [
                    'label' => '(-) Tuli KOnduktif',
                    'value' => '(-) Tuli KOnduktif'
                ],
                [
                    'label' => '(+) tuli saraf',
                    'value' => '(+) tuli saraf'
                ]
            ]
        ],
        [
            'label'          => 'Tes Penala Weber',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Normal',
                    'value' => 'Normal'
                ],
                [
                    'label' => 'Tuli Konduktif',
                    'value' => 'Tuli Konduktif'
                ],
                [
                    'label' => 'Tuli saraf',
                    'value' => 'Tuli saraf'
                ]
            ]
        ]
    ]
];
