<?php 

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label' => 'HIVAntibodyTest',
    'name' => 'HIV Antibody Test',
    'dynamic_forms' => [
        [
            'label' => 'Tanggal Pemeriksaan',
            'key' => 'value',
            'component_name' => Survey::TYPE_DATE_PICKER,
            'default_value' => null,
            'attribute' => null,
            'rule' => null,
            'options' => null
        ],
        [
            'label' => 'Jenis Tes HIV',
            'key' => 'value',
            'component_name' => Survey::TYPE_INPUT_TEXT,
            'default_value' => null,
            'attribute' => null,
            'rule' => null,
            'options' => null
        ],
        [
            'label' => 'Urutan Tes',
            'key' => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value' => null,
            'attribute' => null,
            'rule' => null,
            'options' => [
                ['label' => 'Tes 1', 'value' => 'Tes 1'],
                ['label' => 'Tes 2', 'value' => 'Tes 2'],
                ['label' => 'Tes 3', 'value' => 'Tes 3'],
                ['label' => 'Ulangan', 'value' => 'Ulangan']
            ]
        ],
        [
            'label' => 'Hasil Tes',
            'key' => 'value',
            'component_name' => Survey::TYPE_SELECT,
            'default_value' => null,
            'attribute' => null,
            'rule' => null,
            'options' => [
                ['label' => 'Reaktif', 'value' => 'Reaktif'],
                ['label' => 'Non Reaktif', 'value' => 'Non Reaktif'],
                ['label' => 'Invalid', 'value' => 'Invalid'],
                ['label' => 'Perlu Ulang', 'value' => 'Perlu Ulang']
            ]
        ],
        [
            'label' => 'Nama Reagen',
            'key' => 'value',
            'component_name' => Survey::TYPE_INPUT_TEXT, // bebas diisi
            'default_value' => null,
            'attribute' => null,
            'rule' => null,
            'options' => null
        ],
    ]
];
