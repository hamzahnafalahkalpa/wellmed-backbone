<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'PUMA',
    'name'  => 'PENGKAJIAN NUTRISI (SHORT FROM MNA)',
    'dynamic_forms'  => [
        [
            'label'          => 'Apakah peserta pernah merokok?',
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
            'label'          => 'Berapa rata-rata jumlah batang rokok per hari?',
            'key'            => 'value',
            'component_name' => Survey::TYPE_INPUT_NUMBER,
            'default_value'  => null,
            'attribute'      => [
                'mode'    => 'number'
            ],
            'rule'           => null
        ],
        [
            'label'          => 'Lama merokok dalam tahun',
            'key'            => 'value',
            'component_name' => Survey::TYPE_INPUT_NUMBER,
            'default_value'  => null,
            'attribute'      => [
                'mode'    => 'number'
            ],
            'rule'           => null
        ],
        [
            'label'          => 'Apakah Dokter atau tenaga medis lainnya pernah meminta peserta untuk melakukan pemeriksaan spirometri atau
            peak flow meter (meniup ke dalam suatu alat) untuk mengetahui fungsi paru peserta?',
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
            'label'          => 'Apakah peserta pernah merasa napas pendek ketika peserta berjalan lebih cepat pada jalan yang datar atau
            pada jalan yang sedikit menanjak?',
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
            'label'          => 'Apakah peserta biasanya batuk saat peserta sedang tidak menderita selesma/flu?',
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
            'label'          => 'Status merokok',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Merokok',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak merokok',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Apakah peserta biasanya mempunyai dahak yang berasal dari paru atau kesulitan mengeluarkan dahak saat
            peserta sedang tidak menderita selesma/flu?',
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
