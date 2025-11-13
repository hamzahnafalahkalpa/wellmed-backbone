<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'HIV',
    'name'  => 'KAJIAN TINGKAT RISIKO',
    'dynamic_forms'  => [
        [
            'label'          => 'Punya Pasangan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 'Ya'
                ],
                [
                    'label' => 'Tidak',
                    'value' => 'Tidak'
                ]
            ]
        ],
        [
            'label'          => 'Nama Pasangan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_INPUT_TEXT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
            ]
        ],
        [
            'label'          => 'Apakah Pasangan Hamil',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 'Ya'
                ],
                [
                    'label' => 'Tidak',
                    'value' => 'Tidak'
                ],
                [
                    'label' => 'Tidak Tahu',
                    'value' => 'Tidak Tahu'
                ]
            ]
        ],
        [
            'label'          => 'Kondisi Pasangan Saat Ini',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Positif HIV',
                    'value' => 'Positif HIV',
                ],
                [
                    'label' => 'Negatif HIV',
                    'value' => 'Negatif HIV'
                ],
                [
                    'label' => 'Tidak Tahu',
                    'value' => 'Tidak Tahu'
                ]
            ]
        ],
        [
            'label'          => 'Penyimpanan/Kelompok Berisiko Penularan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => "Pekerja Seks Komersial (PSK)",'value' => "Pekerja Seks Komersial (PSK)"],
                ['label' => "Pelanggan Pekerja Seks",'value' => "Pelanggan Pekerja Seks"],
                ['label' => "Lelaki yang berhubungan seks dengan lelaki (LSL/MSM)",'value' => "Lelaki yang berhubungan seks dengan lelaki (LSL/MSM)"],
                ['label' => "Waria",'value' => "Waria"],
                ['label' => "Pasangan dari orang dengan HIV/AIDS (ODHA)",'value' => "Pasangan dari orang dengan HIV/AIDS (ODHA)"],
                ['label' => "Orang dengan pasangan seks berganti-ganti",'value' => "Orang dengan pasangan seks berganti-ganti"],
                ['label' => "Pengguna Napza Suntik (penasun)",'value' => "Pengguna Napza Suntik (penasun)"],
                ['label' => "Orang yang menerima transfusi darah tidak aman",'value' => "Orang yang menerima transfusi darah tidak aman"],
                ['label' => "Bayi yang lahir dari ibu HIV positif",'value' => "Bayi yang lahir dari ibu HIV positif"],
                ['label' => "Remaja dengan perilaku seksual berisiko",'value' => "Remaja dengan perilaku seksual berisiko"],
                ['label' => "Narapidana atau tahanan",'value' => "Narapidana atau tahanan"],
                ['label' => "Pekerja migran dengan akses kesehatan terbatas",'value' => "Pekerja migran dengan akses kesehatan terbatas"],
                ['label' => "Tenaga kesehatan yang terpapar cairan tubuh (jarum suntik dll)",'value' => "Tenaga kesehatan yang terpapar cairan tubuh (jarum suntik dll)"]
            ]
        ],
        [
            'label'          => 'Populasi Khusus/Warga Binaan Permasyarakatan (WBP)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 'Ya',
                ],
                [
                    'label' => 'Tidak',
                    'value' => 'Tidak'
                ]
            ]
        ],
        [
            'label'          => 'Apakah pasien penderita baru',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Baru',
                    'value' => 'Baru',
                ],
                [
                    'label' => 'Lama',
                    'value' => 'Lama'
                ]
            ]
        ],
        [
            'label'          => 'Alasan Tes HIV',
            'key'            => 'value',
            'component_name' => Survey::TYPE_TEXTAREA,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
            ]
        ],
        [
            'label'          => 'Hubungan seks vaginal berisiko',
            'key'            => 'value',
            'component_name' => 'VaginalSexRisk',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
            //     [
            //         'label' => 'Ya',
            //         'value' => 'Ya',
            //         'dynamic_forms' => [
            //             [
            //                 'label'          => 'Tanggal',
            //                 'key'            => 'value',
            //                 'component_name' => Survey::TYPE_DATE,
            //                 'default_value'  => null,
            //                 'attribute'      => null,
            //                 'rule'           => null,
            //             ]
            //         ]
            //     ],
            //     [
            //         'label' => 'Tidak',
            //         'value' => 'Tidak'
            //     ]
            ]
        ],
        [
            'label'          => 'Bergantian peralatan suntik',
            'key'            => 'value',
            'component_name' => 'SyringeExchangeRisk',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
            //     [
            //         'label' => 'Ya',
            //         'value' => 'Ya',
            //         'dynamic_forms' => [
            //             [
            //                 'label'          => 'Tanggal',
            //                 'key'            => 'value',
            //                 'component_name' => Survey::TYPE_DATE,
            //                 'default_value'  => null,
            //                 'attribute'      => null,
            //                 'rule'           => null,
            //             ]
            //         ]
            //     ],
            //     [
            //         'label' => 'Tidak',
            //         'value' => 'Tidak'
            //     ]
            ]
        ],
        [
            'label'          => 'Transmisi ibu ke anak',
            'key'            => 'value',
            'component_name' => 'BloodTransfusionRisk',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                // [
                //     'label' => 'Ya',
                //     'value' => 'Ya',
                //     'dynamic_forms' => [
                //         [
                //             'label'          => 'Tanggal',
                //             'key'            => 'value',
                //             'component_name' => Survey::TYPE_DATE,
                //             'default_value'  => null,
                //             'attribute'      => null,
                //             'rule'           => null,
                //         ]
                //     ]
                // ],
                // [
                //     'label' => 'Tidak',
                //     'value' => 'Tidak'
                // ]
            ]
        ],
        [
            'label'          => 'Periode jendela',
            'key'            => 'value',
            'component_name' => 'WindowPeriodRisk',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                // [
                //     'label' => 'Ya',
                //     'value' => 'Ya',
                //     'dynamic_forms' => [
                //         [
                //             'label'          => 'Tanggal',
                //             'key'            => 'value',
                //             'component_name' => Survey::TYPE_DATE,
                //             'default_value'  => null,
                //             'attribute'      => null,
                //             'rule'           => null,
                //         ]
                //     ]
                // ],
                // [
                //     'label' => 'Tidak',
                //     'value' => 'Tidak'
                // ]
            ]
        ],
        [
            'label'          => 'Anal seks berisiko',
            'key'            => 'value',
            'component_name' => 'AnalSexRisk',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                // [
                //     'label' => 'Ya',
                //     'value' => 'Ya',
                //     'dynamic_forms' => [
                //         [
                //             'label'          => 'Tanggal',
                //             'key'            => 'value',
                //             'component_name' => Survey::TYPE_DATE,
                //             'default_value'  => null,
                //             'attribute'      => null,
                //             'rule'           => null,
                //         ]
                //     ]
                // ],
                // [
                //     'label' => 'Tidak',
                //     'value' => 'Tidak'
                // ]
            ]
        ],
        [
            'label'          => 'Transfusi darah',
            'key'            => 'value',
            'component_name' => 'TransfusionRisk',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                // [
                //     'label' => 'Ya',
                //     'value' => 'Ya',
                //     'dynamic_forms' => [
                //         [
                //             'label'          => 'Tanggal',
                //             'key'            => 'value',
                //             'component_name' => Survey::TYPE_DATE,
                //             'default_value'  => null,
                //             'attribute'      => null,
                //             'rule'           => null,
                //         ]
                //     ]
                // ],
                // [
                //     'label' => 'Tidak',
                //     'value' => 'Tidak'
                // ]
            ]
        ],
        [
            'label'          => 'Pernah tes HIV sebelumnya',
            'key'            => 'value',
            'component_name' => 'PreviousHIVTest',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                // [
                //     'label' => 'Ya',
                //     'value' => 'Ya',
                //     'dynamic_forms' => [
                //         [
                //             'label'          => 'Lokasi tes HIV sebelumnya',
                //             'key'            => 'value',
                //             'component_name' => Survey::TYPE_INPUT,
                //             'default_value'  => null,
                //             'attribute'      => null,
                //             'rule'           => null,
                //         ]
                //     ]
                // ],
                // [
                //     'label' => 'Tidak',
                //     'value' => 'Tidak'
                // ]
            ]
        ],
        [
            'label'          => 'Kesediaan untuk test',
            'key'            => 'value',
            'component_name' => 'WillingHIVToTest',
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                // [
                //     'label' => 'Ya',
                //     'value' => 'Ya',
                //     'forms' => [
                //         'HIVAntibodyTest'
                //     ]
                // ],
                // [
                //     'label' => 'Tidak',
                //     'value' => 'Tidak'
                // ]
            ]
        ]
    ]
];
