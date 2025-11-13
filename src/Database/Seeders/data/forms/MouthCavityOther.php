<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'MouthCavityOther',
    'name'  => 'KEADAAN GUSI, KEBERSIHAN GIGI,DAN KONDISI LAINNYA',
    'dynamic_forms'  => [
        [
            'label'          => 'Gusi Sehat',
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
            'label'          => 'Gusi Bengkak/Abses/Fistel',
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
            'label'          => 'Karang gigi',
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
            'label'          => 'Gigi Kotor (ada plak & sisa makanan)',
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
            'label'          => 'Susunan gigi depan tidak teratur',
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
            'label'          => 'Gusi Mudah Berdarah',
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
        ]

    ]
];
