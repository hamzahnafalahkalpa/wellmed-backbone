<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'SNST',
    'name'  => 'FORMULIR GIZI SIMPLE NUTRITION SCREENING TOOL (SNST)',
    'dynamic_forms'  => [
        [
            'label'          => 'Apakah Anda merasakan lemah, loyo dan tidak bertenaga?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah akhir-akhir ini Anda kehilangan berat badan secara tidak sengaja (6 bulan terakhir)?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah Anda mengalami penurunan asupan makan selama 1 minggu terakhir?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah Anda menderita suatu penyakit yang mengakibatkan adanya perubahan jumlah atau jenis makanan yang Anda makan?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah pakaian anda terasa lebih longgar?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak',
                    'value' => 0
                ]
            ]
        ]
    ]
];
