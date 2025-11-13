<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'ISKJ',
    'name'  => 'INSTRUMENT SKRINING KESEHATAN JIWA (ISKJ)',
    'dynamic_forms'  => [
        [
            'label'          => 'Apakah Anda mudah lelah?',
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
            'label'          => 'Apakah Anda mengalami kesulitan untuk mengambil keputusan?',
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
            'label'          => 'Apakah Anda merasa tidak enak di perut?',
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
            'label'          => 'Apakah Anda merasa tidak berharga?',
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
            'label'          => 'Apakah Anda merasa sulit untuk menikmati aktivitas sehari-hari?',
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
    ]
];
