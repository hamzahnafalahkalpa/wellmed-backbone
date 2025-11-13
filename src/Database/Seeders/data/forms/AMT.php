<?php



use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'AMT',
    'name'  => 'ABBREVIATED MENTAL TEST (AMT)',
    'dynamic_forms'  => [
        [
            'label'          => 'Mengenali orang lain di RS. (dokter, perawat, dll)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Menghitung terbalik (20 s/d 1)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Nama Presiden RI',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Saat ini berada di mana',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Tahun kelahiran pasien atau anak terakhir',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Umur',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Tahun ini',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Tahun kemerdekaan RI',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Waktu / jam sekarang',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ],
        [
            'label'          => 'Alamat tempat tinggal',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Salah',
                    'value' => 0
                ],
                [
                    'label' => 'Benar',
                    'value' => 1
                ]
            ]
        ]
    ]
];