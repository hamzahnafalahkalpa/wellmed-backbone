<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'PostpartumObservation',
    'name'  => 'PELAYANAN KESEHATAN IBU NIFAS',
    'dynamic_forms'  => [
        [
            'label'          => 'Menanyakan kondisi ibu nifas secara umum',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pengukuran tekanan darah, suhu tubuh, pernafasan, dan nadi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pemeriksaan lokhia dan perdarahan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pemeriksaan kondisi jalan lahir dan tanda infeksi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pemeriksaan kontraksi rahim dan tinggi fundus uteri',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pemeriksaan payudara dan anjuran pemberian ASI eksklusif',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pemberian kapsul vitamin A (2 kapsul)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Pelayanan kontrasepsi pasca persalinan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Konseling',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Tatalaksana pada ibu nifas sakit atau ibu nifas dengan komplikasi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ],
        [
            'label'          => 'Memberikan nasehat',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 'Dilakukan'
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 'Dilakukan sesuai indikasi'
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 'Tidak dilakukan'
                ]
            ]
        ]
    ]
];
