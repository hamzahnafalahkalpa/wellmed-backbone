<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'ThoraxExamination',
    'name'  => 'PEMERIKSAAN FOTO TORAKS',
    'dynamic_forms'  => [
        [
            'label'          => 'Kondisi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Abnormalitas TBC',
                    'value' => 'Abnormalitas TBC'
                ],
                [
                    'label' => 'Abnormalitas Bukan TBC',
                    'value' => 'Abnormalitas Bukan TBC'
                ],
                [
                    'label' => 'Normal',
                    'value' => 'Normal'
                ]
            ]
        ],
        [
            'label'          => 'Tanggal Pemeriksaan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_DATE_PICKER,
            'default_value'  => null,
            'attribute'      => [
            ],
            'rule'           => null,
            'options'        => null
        ],
        [
            'label'          => 'No.Seri',
            'key'            => 'value',
            'component_name' => Survey::TYPE_DATE_PICKER,
            'default_value'  => null,
            'attribute'      => [
            ],
            'rule'           => null,
            'options'        => null
        ],
        [
            'label'          => 'Saran',
            'key'            => 'value',
            'component_name' => Survey::TYPE_TEXTAREA,
            'default_value'  => null,
            'rule'           => null,
            'options'        => null
        ]
    ]
];
