<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label' => 'NeonatalEsensial',
    'name' => 'Neonatal Esensial',
    'dynamic_forms' => [
        [
            'label' => 'Anak ke',
            'key' => 'anak_ke',
            'component_name' => Survey::TYPE_INPUT_NUMBER,
            'attribute' => [
                'mode' => 'number' //number, currency, text
            ],
            'default_value' => null,
            'options' => null, // integer, contoh: 1, 2, 3, ...
        ],
        [
            'label' => 'Berat Badan (kg)',
            'key' => 'weight',
            'type' => Survey::TYPE_INPUT_NUMBER,
            'attribute' => [
                'mode' => 'number'
            ],
            'default_value' => null,
            'options' => null, // kg
        ],
        [
            'label' => 'Tinggi Badan (cm)',
            'key' => 'height',
            'type' => Survey::TYPE_INPUT_NUMBER,
            'attribute' => [
                'mode' => 'number'
            ],
            'default_value' => null,
            'options' => null, // cm
        ],
        [
            'label' => 'Keadaan Bayi',
            'key' => 'keadaan_bayi',
            'type' => Survey::TYPE_SELECT,
            'default_value' => null,
            'options' => [
                ['label' => 'Sehat', 'value' => 'Sehat'],
                ['label' => 'Sakit', 'value' => 'Sakit'],
            ],
        ],
        [
            'label' => 'Jenis Kelamin',
            'key' => 'jenis_kelamin',
            'type' => Survey::TYPE_SELECT,
            'default_value' => null,
            'options' => [
                ['label' => 'Laki Laki', 'value' => 'Laki Laki'],
                ['label' => 'Perempuan', 'value' => 'Perempuan'],
            ],
        ],
        [
            'label' => 'Kondisi Bayi',
            'key' => 'kondisi_bayi',
            'type' => Survey::TYPE_CHECKBOX,
            'default_value' => null,
            'options' => [
                ['label' => 'Menangis Kuat', 'value' => 'Menangis Kuat'],
                ['label' => 'Menangis Beberapa Saat', 'value' => 'Menangis Beberapa Saat'],
                ['label' => 'Tidak Menangis', 'value' => 'Tidak Menangis'],
                ['label' => 'Seluruh Tubuh Kemerahan', 'value' => 'Seluruh Tubuh Kemerahan'],
                ['label' => 'Tubuh Biru', 'value' => 'Tubuh Biru'],
                ['label' => 'Tidak Merah', 'value' => 'Tidak Merah'],
            ],
        ],
        [
            'label' => 'Penilaian Bayi',
            'key' => 'penilaian_bayi',
            'type' => Survey::TYPE_SELECT,
            'default_value' => null,
            'options' => [
                ['label' => 'Normal', 'value' => 'Normal'],
                ['label' => 'Ada penyulit', 'value' => 'Ada penyulit'],
            ],
        ],
        [
            'label' => 'Asfiksia',
            'key' => 'asfiksia',
            'type' => Survey::TYPE_CHECKBOX,
            'default_value' => null,
            'options' => [
                ['label' => 'Biru', 'value' => 'Biru'],
                ['label' => 'Pucat', 'value' => 'Pucat'],
                ['label' => 'Lemas', 'value' => 'Lemas'],
            ],
        ],
        [
            'label' => 'Tindakan dari Asfiksia',
            'key' => 'tindakan_dari_asfiksia',
            'type' => Survey::TYPE_CHECKBOX,
            'default_value' => null,
            'options' => [
                ['label' => 'Bebaskan Jalan Nafas', 'value' => 'Bebaskan Jalan Nafas'],
                ['label' => 'Rangsang', 'value' => 'Rangsang'],
                ['label' => 'Ventilasi Tekanan Positif', 'value' => 'Ventilasi Tekanan Positif'],
            ],
        ],
        [
            'label' => 'Tindakan Lainnya',
            'key' => 'tindakan_lainnya',
            'type' => Survey::TYPE_TEXTAREA,
            'default_value' => null,
            'options' => null, // teks bebas
        ],
        [
            'label' => 'Pemberian ASI',
            'key' => 'pemberian_asi',
            'type' => Survey::TYPE_SELECT,
            'default_value' => null,
            'options' => [
                ['label' => 'Ya', 'value' => 'Ya'],
                ['label' => 'Tidak', 'value' => 'Tidak'],
            ],
        ],
        [
            'label' => 'Komplikasi Bayi',
            'key' => 'komplikasi_bayi',
            'type' => Survey::TYPE_TEXTAREA,
            'default_value' => null,
            'options' => null, // teks bebas
        ],
        [
            'label' => 'Cacat Bawaan',
            'key' => 'cacat_bawaan',
            'type' => Survey::TYPE_TEXTAREA,
            'default_value' => null,
            'options' => null, // teks bebas
        ],
        [
            'label' => 'Masalah Lain',
            'key' => 'masalah_lain',
            'type' => Survey::TYPE_TEXTAREA,
            'default_value' => null,
            'options' => null, // teks bebas
        ],
        [
            'label' => 'Hasilnya',
            'key' => 'hasilnya',
            'type' => Survey::TYPE_TEXTAREA,
            'default_value' => null,
            'options' => null, // teks bebas (misalnya: Dirujuk, Rawat Inap)
        ],
    ],
];
