<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'GDS4',
    'name'  => 'ABBREVIATED MENTAL TEST (GDS4)',
    'dynamic_forms'  => [
        [
            'label'          => 'Apakah Anda sebenarnya cukup puas dengan hidup Anda?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 0
                ],
                [
                    'label' => 'Tidak',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Apakah Anda merasa tidak berharga seperti perasaan anda saat ini?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 0
                ],
                [
                    'label' => 'Tidak',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Apakah Anda sering merasa tidak berdaya?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 0
                ],
                [
                    'label' => 'Tidak',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Apakah Anda sering merasa bosan?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 0
                ],
                [
                    'label' => 'Tidak',
                    'value' => 1
                ]
            ]
        ]
    ]
];