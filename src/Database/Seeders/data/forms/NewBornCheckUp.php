<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'NewBornCheckUp',
    'name'  => 'PEMERIKSAAN BAYI BARU LAHIR',
    'dynamic_forms'  => [
        [
            'label'          => 'Pemeriksaan dengan bagan MTBS (bayi muda)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Menanyakan kondisi bayi baru lahir secara umum',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Memeriksa tanda bahaya dan identifikasi kuning',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Pengukuran berat badan, panjang badan dan lingkar kepala',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Mendampingi inisiasi menyusu dini',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Memantau dan Konseling menyusui',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Pemotongan dan perawatan tali pusat',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Pemberian imunisasi hepatitis B0',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Skrining Hipotiroid Kongenital (SHK)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Pencegahan Penularan dari Ibu ke Anak (HIV, Sifilis, Hepatitis B)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ],
        [
            'label'          => 'Merujuk kasus yang tidak dapat ditangani dalam kondisi stabil, tepat waktu ke fasilitas pelayanan kesehatan yang lebih mampu',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                ['label' => 'Dilakukan', 'value' => 0],
                ['label' => 'Dilakukan sesuai indikasi', 'value' => 1],
                ['label' => 'Tidak dilakukan', 'value' => 2]
            ]
        ]
    ]
];
