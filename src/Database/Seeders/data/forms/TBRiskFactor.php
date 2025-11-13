<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'TBRiskFactor',
    'name'  => 'FAKTOR RISIKO TBC',
    'dynamic_forms'  => [
        [
            'label'          => 'Pernah Terdiagnosis / Berobat TBC?',
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
            'label'          => 'Ibu Hamil',
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
            'label'          => 'ODHIV',
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
            'label'          => 'Pernah Berobat TBC, tapi tidak tuntas?',
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
            'label'          => 'Lansian > 65',
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
            'label'          => 'Tinggal diwilayah kumuh miskin ?',
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
            'label'          => 'Merokok / Peroko Pasif',
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
            'label'          => 'Riwayat DM / Kencing Manis',
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
            'label'          => 'Malnutrisi',
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
            'label'          => 'Warga Binaan Pemastarakatam (WBP)',
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
