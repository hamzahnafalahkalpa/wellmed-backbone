<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'MorseFallScale',
    'name'   => 'Risiko Jatuh',
    'dynamic_forms'  => [
        [
            'label'          => 'Riwayat jatuh dalam 3 bulan terakhir',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Ya', 'value' => 25 ],
                [ 'label' => 'Tidak', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Diagnosis sekunder (lebih dari 1)',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Ya', 'value' => 15 ],
                [ 'label' => 'Tidak', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Alat bantu berjalan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Tidak ada / tirah baring / dibantu perawat', 'value' => 0 ],
                [ 'label' => 'Kruk / tongkat / walker', 'value' => 15 ],
                [ 'label' => 'Berpegangan pada perabot (furnitures)', 'value' => 30 ],
            ]
        ],
        [
            'label'          => 'Terpasang infus / heparin lock',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Ya', 'value' => 20 ],
                [ 'label' => 'Tidak', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Cara berjalan / gaya berjalan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Normal / tirah baring / kursi roda', 'value' => 0 ],
                [ 'label' => 'Lemah', 'value' => 10 ],
                [ 'label' => 'Terganggu', 'value' => 20 ],
            ]
        ],
        [
            'label'          => 'Status mental',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Menyadari keterbatasan diri', 'value' => 0 ],
                [ 'label' => 'Tidak realistis terhadap kondisi (overestimasi kemampuan diri)', 'value' => 15 ],
            ]
        ]
    ]
];
