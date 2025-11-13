<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'ADL',
    'name'  => 'PENILAIAN ACTIVITY OF DAILY LIVING (ADL)',
    'dynamic_forms'  => [
        [
            'label'          => 'Mengendalikan rangsang Buang Air Besar (BAB)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang - kadang tak terkendali (1 x / minggu)',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak terkendali',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Makan minum (jika makan harus berupa potongan, dianggap dibantu)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Perlu ditolong memotong',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak mampu',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Penggunaan WC (keluar masuk WC, melepas/memakai celana, cebok, menyiram)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Perlu pertolongan pada beberapa Kegiatan tetapi dapat mengerjakan sendiri',
                    'value' => 1
                ],
                [
                    'label' => 'Tergantung pertolongan orang lain',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Bergerak dari kursi roda ke tempat tidur dan sebaliknya (termasuk duduk di tempat tidur)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 3
                ],
                [
                    'label' => 'Bantuan minimal 1 orang',
                    'value' => 2
                ],
                [
                    'label' => 'Perlu banyak bantuan untuk bisa duduk (2 orang)',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak mampu',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Membersihkan diri (mencuci wajah, menyikat Rambut, mencukur kumis, sikat gigi)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Butuh pertolongan orang lain',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Mengendalikan rangsang Buang Air Kecil (BAK)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Kadang-kadang tak terkendali (1 x / 24jam)',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak terkendali',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Naik turun tangga',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Butuh pertolongan',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak mampu',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Mandi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak mampu',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Berpakaian (termasuk memasang tali sepatu, Mengencangkan sabuk)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 2
                ],
                [
                    'label' => 'Sebagian dibantu (mis, memengancing baju)',
                    'value' => 1
                ],
                [
                    'label' => 'Tergantung orang lain',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Berjalan di tempat rata (atau jika tidak bisa berjalan, menjalankan kursi roda)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => [
                'label' => 'Mandiri',
                'value' => 2
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Mandiri',
                    'value' => 3
                ],
                [
                    'label' => 'Bantuan minimal 1 orang',
                    'value' => 2
                ],
                [
                    'label' => 'Bisa (pindah) dengan kursi roda',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak mampu',
                    'value' => 0
                ]
            ]
        ]
    ]
];