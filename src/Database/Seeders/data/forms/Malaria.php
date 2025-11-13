<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'Malaria',
    'name'  => 'Identifikasi Malaria',
    'dynamic_forms'  => [
        [
            'label'          => 'Pernahkah pasien menderita penyakit malaria sebelumnya?*',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => [
                'label' => 'Pilih Jawaban',
                'value' => null
            ],
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Pilih Jawaban',
                    'value' => null
                ],
                [
                    'label' => 'Belum Pernah',
                    'value' => 0
                ],
                [
                    'label' => 'Pernah 1 - 2 kali',
                    'value' => 1
                ],
                [
                    'label' => 'Pernah 3 kali atau lebih',
                    'value' => 2
                ]
            ]
        ],
        [
            'label'          => 'Apakah sebelum sakit, 2 minggu yang lalu pernah berkunjung ke tempat/kota lain?*',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Ya',
                    'value' => 1,
                    'note' => null
                ],
                [
                    'label' => 'Tidak',
                    'value' => 0
                ],
            ]
        ],
    ]
];