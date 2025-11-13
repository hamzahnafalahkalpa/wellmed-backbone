<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'PhysicalActivity',
    'name'   => 'Aktifitas Harian',
    'dynamic_forms'  => [
        [
            'label'          => 'Makan dan Minum',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Tergantung/Tidak Mampu', 'value' => 4 ],
                [ 'label' => 'Perlu bantuan orang lain dan Alat', 'value' => 3 ],
                [ 'label' => 'Perlu bantuan orang lain', 'value' => 2 ],
                [ 'label' => 'Dibantu sebagian', 'value' => 1 ],
                [ 'label' => 'Mandiri', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Toileting',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Tergantung/Tidak Mampu', 'value' => 4 ],
                [ 'label' => 'Perlu bantuan orang lain dan Alat', 'value' => 3 ],
                [ 'label' => 'Perlu bantuan orang lain', 'value' => 2 ],
                [ 'label' => 'Dibantu sebagian', 'value' => 1 ],
                [ 'label' => 'Mandiri', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Mandi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Tergantung/Tidak Mampu', 'value' => 4 ],
                [ 'label' => 'Perlu bantuan orang lain dan Alat', 'value' => 3 ],
                [ 'label' => 'Perlu bantuan orang lain', 'value' => 2 ],
                [ 'label' => 'Dibantu sebagian', 'value' => 1 ],
                [ 'label' => 'Mandiri', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Berpakaian',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Tergantung/Tidak Mampu', 'value' => 4 ],
                [ 'label' => 'Perlu bantuan orang lain dan Alat', 'value' => 3 ],
                [ 'label' => 'Perlu bantuan orang lain', 'value' => 2 ],
                [ 'label' => 'Dibantu sebagian', 'value' => 1 ],
                [ 'label' => 'Mandiri', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Mobilisasi',
            'key'            => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [ 'label' => 'Tergantung/Tidak Mampu', 'value' => 4 ],
                [ 'label' => 'Perlu bantuan orang lain dan Alat', 'value' => 3 ],
                [ 'label' => 'Perlu bantuan orang lain', 'value' => 2 ],
                [ 'label' => 'Dibantu sebagian', 'value' => 1 ],
                [ 'label' => 'Mandiri', 'value' => 0 ],
            ]
        ],
        [
            'label'          => 'Catatan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_TEXTAREA,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
        ]
    ]
];
