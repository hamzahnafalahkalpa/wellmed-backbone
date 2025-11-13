<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'MNA',
    'name'  => 'PENGKAJIAN NUTRISI (SHORT FROM MNA)',
    'dynamic_forms'  => [
        [
            'label'          => 'Mobilitas',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Bisa keluar rumah',
                    'value' => 2
                ],
                [
                    'label' => 'Bisa keluar dari tempat tidur atau kursi roda, tetapi tidak bisa keluar rumah',
                    'value' => 1
                ],
                [
                    'label' => 'Harus berbaring di tempat tidur atau menggunakan kursi roda',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Penurunan berat badan dalam 3 bulan terakhir',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Tidak ada penurunan berat badan',
                    'value' => 3
                ],
                [
                    'label' => 'Penurunan berat badan 1-3 kg',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak tahu',
                    'value' => 1
                ],
                [
                    'label' => 'Penurunan berat badan lebih dari 3 kg',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Masalah neuropsikologi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Tidak ada masalah psikologis',
                    'value' => 2
                ],
                [
                    'label' => 'Demensia ringan',
                    'value' => 1
                ],
                [
                    'label' => 'Demensia berat atau depresi berat',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah ada penurunan asupan makanan dalam jangka waktu 3 bulan oleh karena kehilangan nafsu makan, masalah
        pencernaan, kesulitan menelan atau mengunyah?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Nafsu makan biasa saja',
                    'value' => 2
                ],
                [
                    'label' => 'Nafsu makan sedikit berkurang (sedang)',
                    'value' => 1
                ],
                [
                    'label' => 'Nafsu makan yang sangat berkurang',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Indeks Massa Tubuh (IMT) berat badan dalam kg/tinggi badan dalam',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'MT 23 atau lebih',
                    'value' => 3
                ],
                [
                    'label' => 'IMT 21 - < 23',
                    'value' => 2
                ],
                [
                    'label' => 'IMT 19 - < 21',
                    'value' => 1
                ],
                [
                    'label' => 'IMT < 19',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => '6 Menderita stress psikologis/penyakit akut dalam 3 bulan terakhir:',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Tidak',
                    'value' => 2
                ],
                [
                    'label' => 'Ya',
                    'value' => 0
                ]
            ]
        ]
    ]
];
