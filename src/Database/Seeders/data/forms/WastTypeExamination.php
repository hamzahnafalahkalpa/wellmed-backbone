<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'WastTypeExamination',
    'name'  => 'JENIS KEKERASAN TERHADAP PEREMPUAN',
    'dynamic_forms'  => [
        [
            'label'          => 'Kekerasan Psikis',
            'key'            => 'value',
            'component_name' => Survey::TYPE_CHECKBOX,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Kekerasan Psikis',
                    'value' => 'Kekerasan Psikis'
                ],
                [
                    'label' => 'Diskriminatif',
                    'value' => 'Diskriminatif'
                ],
                [
                    'label' => 'Lainnya',
                    'value' => 'Lainnya'
                ]
            ]
        ],
        [
            'label'          => 'Kekerasan Psikis Lainnya',
            'key'            => 'value',
            'component_name' => Survey::TYPE_INPUT_TEXT,
            'default_value'  => null,
            'attribute'      => [
                'placeholder' => 'Lainnya'
            ],
            'rule'           => null,
            'options'        => null
        ],
        [
            'label'          => 'Kekerasan Fisik',
            'key'            => 'value',
            'component_name' => Survey::TYPE_CHECKBOX,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Tumpul',
                    'value' => 'Tumpul'
                ],
                [
                    'label' => 'Tajam',
                    'value' => 'Tajam'
                ],
                [
                    'label' => 'Luka Bakar',
                    'value' => 'Luka Bakar'
                ],
                [
                    'label' => 'Eskploitasi',
                    'value' => 'Eskploitasi'
                ],
                [
                    'label' => 'Lainnya',
                    'value' => 'Lainnya'
                ]
            ]
        ],
        [
            'label'          => 'Kekerasan Fisik Lainnya',
            'key'            => 'value',
            'component_name' => Survey::TYPE_INPUT_TEXT,
            'default_value'  => null,
            'attribute'      => [
                'placeholder' => 'Lainnya'
            ],
            'rule'           => null,
            'options'        => null
        ],
        [
            'label'          => 'Kekerasan Seksual',
            'key'            => 'value',
            'component_name' => Survey::TYPE_CHECKBOX,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Pencabulan',
                    'value' => 'Pencabulan'
                ],
                [
                    'label' => 'Perkosaan',
                    'value' => 'Perkosaan'
                ],
                [
                    'label' => 'Eksploitasi',
                    'value' => 'Eksploitasi'
                ],
                [
                    'label' => 'Lainnya',
                    'value' => 'Lainnya'
                ]
            ]
        ],
        [
            'label'          => 'Kekerasan Seksual Lainnya',
            'key'            => 'value',
            'component_name' => Survey::TYPE_INPUT_TEXT,
            'default_value'  => null,
            'attribute'      => [
                'placeholder' => 'Lainnya'
            ],
            'rule'           => null,
            'options'        => null
        ]
    ]
];
