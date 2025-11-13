<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'ANCTerpadu',
    'name'  => 'Pemeriksaan 10T saat ANC Plus USG',
    'dynamic_forms'  => [
        [
            'label'          => 'Timbang Berat Badan dan Ukur Tinggi Badan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Ukur Tekanan Darah',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Ukur Lingkar Lengan Atas (LILA)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Pemeriksaan Tinggi Fundus (penilaian usia/ besar janin)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Tentukan Presentase dan Denyut Jantung Janin (DJJ)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Pemberian Imunisasi Tetanus Teksoid (TT)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Pemberian Tablet Tambah Darah (TTD)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Pemeriksaan Laboratorium (Termasuk status Anemia)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Tata laksana kasus',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Temu Wicara/konseling',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'USG Obstetri Dasar Terbatas',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Tidak dilakukan',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Dilakukan',
                    'value' => 0
                ],
                [
                    'label' => 'Dilakukan sesuai indikasi',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 2
                ]
            ]
        ]
    ]
];
