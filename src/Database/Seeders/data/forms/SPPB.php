<?php

use Hanafalah\ModuleExamination\Models\Form\Survey;

return [
    'label'  => 'SPPB',
    'name'  => 'SHORT PHYSICAL PERFORMANCE BATTERY (SPPB)',
    'dynamic_forms'  => [
        [
            'label'          => 'Berdiri tandem',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Bertahan 10 detik',
                    'value' => 3
                ],
                [
                    'label' => 'Bertahan 3 – 9,99 detik',
                    'value' => 2
                ],
                [
                    'label' => 'Bertahan <3 detik',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Tes kecepatan berjalan: waktu untuk berjalan sejauh empat meter',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => '<4,82 detik',
                    'value' => 4
                ],
                [
                    'label' => '4,82 detik – 6,20 detik',
                    'value' => 3
                ],
                [
                    'label' => '6,21 detik – 8,70 detik',
                    'value' => 2
                ],
                [
                    'label' => '>8,70 detik',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak dapat menyelesaikan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Berdiri semi-tandem',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Betahan 10 detik',
                    'value' => 1
                ],
                [
                    'label' => 'Tidak bertahan 10 detik',
                    'value' => 0
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Berdiri berdampingan',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => 'Betahan 10 detik',
                    'value' => 2
                ],
                [
                    'label' => 'Tidak bertahan 10 detik',
                    'value' => 0
                ],
                [
                    'label' => 'Tidak dilakukan',
                    'value' => 0
                ]
            ]
        ],
        [
            'label'          => 'Tes berdiri dari kursi: waktu untuk bangkit dari kursi lima kali',
            'key'            => 'value',
            'component_name' => Survey::TYPE_RADIO_BUTTON,
            'default_value'  => null,
            'attribute'      => null,
            'rule'           => null,
            'options'        => [
                [
                    'label' => '<11,19 detik',
                    'value' => 4
                ],
                [
                    'label' => '11,2 – 13,69 detik',
                    'value' => 3
                ],
                [
                    'label' => '13,7 – 16,69 detik',
                    'value' => 2
                ],
                [
                    'label' => '16,7 – 59,9 detik',
                    'value' => 1
                ],
                [
                    'label' => '>60 detik atau tidak dapat menyelesaikan',
                    'value' => 0
                ]
            ]
        ]

    ]
];
