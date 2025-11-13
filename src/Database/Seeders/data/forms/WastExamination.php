<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'WastExamination',
    'name'  => 'PEMERIKSAAN KEKERASAN TERHADAP PEREMPUAN & ANAK',
    'dynamic_forms'  => [
        [
            'label'          => 'Apakah pertengakaran mulut mengakibatkan pasangan ibu memukul, menendang, atau mendorong?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Secara umum bagaimana ibu menggambarkan hubungan ibu dengan pasangan?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Penuh ketegangan',
                    'value' => 2
                ],
                [
                    'label' => 'Agak ada ketegangan',
                    'value' => 2
                ],
                [
                    'label' => 'Tanpa ketegangan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ibu merasa ketakutan terhadap apa yang dikatakan atau dilakukan oleh pasangan ibu?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ibu merasa dibatasi dalam mengatur pembelanjaan rumah tangga?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah pertengakaran mulut mengakibatkan ibu merasa direndahkan atau merasa tidak nyaman dengan diri sendiri?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ibu dan pasangan ibu mengatasi pertengakaran mulut dengan?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sangat Kesulitan',
                    'value' => 2
                ],
                [
                    'label' => 'Agak Kesulitan',
                    'value' => 2
                ],
                [
                    'label' => 'Tanpa kesulitan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah pertengakaran mulut mengakibatkan pasangan ibu memukul, menendang, atau mendorong?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ibu merasa dibatasi dalam mengatur pembelanjaan rumah tangga?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Secara umum bagaimana ibu menggambarkan hubungan ibu dengan pasangan?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Penuh ketegangan',
                    'value' => 2
                ],
                [
                    'label' => 'Agak ada ketengangan',
                    'value' => 2
                ],
                [
                    'label' => 'Tanpa ketegangan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ibu dan pasangan ibu mengatasi pertengakaran mulut dengan?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sangat Kesulitan',
                    'value' => 2
                ],
                [
                    'label' => 'Agak Kesulitan',
                    'value' => 2
                ],
                [
                    'label' => 'Tanpa kesulitan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ibu merasa ketakutan terhadap apa yang dikatakan atau dilakukan oleh pasangan ibu?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah pertengakaran mulut mengakibatkan ibu merasa direndahkan atau merasa tidak nyaman dengan diri sendiri?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Sering',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak pernah',
                    'value' => 0
                ]
            ]
        ]
    ]
];
