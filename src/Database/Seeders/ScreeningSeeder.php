<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleExamination\Contracts\Data\Form\ScreeningData;
use Hanafalah\ModuleMedicService\Contracts\Data\MedicServiceData;
use Hanafalah\ModuleMedicService\Enums\Label;   
use Illuminate\Database\Seeder;

class ScreeningSeeder extends Seeder
{
    use HasRequestData;

    public function run()
    {
        $arr = [
            [
                'id' => null,
                'name' => 'Risiko Penyakit',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'HearingLossHistory',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'BloodSugarTest',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'VisualImpairmentTest',
                        'ordering' => 3
                    ],
                    [
                        'id' => null,
                        'form_label' => 'HemoglobinTest',
                        'ordering' => 4
                    ],
                ]
            ],
            [
                'id'=> null,
                'name' => 'Anemia',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'TTDExamination',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'HemoglobinTest',
                        'ordering' => 2
                    ]
                ]
            ],
            [
                'id'=> null,
                'name' => 'Mulut & gigi',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'Odontogram',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'MouthCavity',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'MouthCavityOther',
                        'ordering' => 3
                    ]                
                ]
            ],
            [
                'id'=> null,
                'name' => 'Mata',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'VisualImpairmentTest',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'EyeExamination',
                        'ordering' => 2
                    ]
                ]
            ],
            [
                'id'=> null,
                'name' => 'Telinga',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'HearingLossHistory',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'HearingFunction',
                        'ordering' => 2
                    ]
                ]
            ],
            [
                'id'=> null,
                'name' => 'Kekerasan anak & perempuan',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'WastExamination',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'WastTypeExamination',
                        'ordering' => 2
                    ]
                ]
            ],
            [
                'id'=> null,
                'name' => 'Kesehatan Jiwa',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'ISKJ',
                        'ordering' => 1
                    ]
                ]
            ],
            [
                'id'=> null,
                'name' => 'Kebugaran',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'SingleTest',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'RocportTest',
                        'ordering' => 2
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'ANC Terpadu',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'ChildAndPregnancyHistory',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'ANCTerpadu',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'TetanusImmunization',
                        'ordering' => 3
                    ],
                    [
                        'id' => null,
                        'form_label' => 'TTDExamination',
                        'ordering' => 4
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Ibu Nifas',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'PostpartumObservation',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'FamilyPlanningService',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'AdministrationVitaminA',
                        'ordering' => 3
                    ],
                    [
                        'id' => null,
                        'form_label' => 'TTDExamination',
                        'ordering' => 4
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Kesehatan Anak',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'AdministrationVitaminA',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'ChildGrowth',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'HearingLossHistory',
                        'ordering' => 3
                    ],
                    [
                        'id' => null,
                        'form_label' => 'ImmunizationHistory',
                        'ordering' => 3
                    ],
                ]
            ],
            [
                'id' => null,
                'name' => 'HIV',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'HIV',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'HIVAntibodyTest',
                        'ordering' => 2
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Diabetes melitus',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'BloodSugarTest',
                        'ordering' => 1
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Gizi',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'SNST',
                        'ordering' => 1
                    ]
                ]
            ],
            // [
            //     'id' => null,
            //     'name' => 'Deteksi Kanker Leher Rahim & Payudara',
            //     'ordering' => null,
            //     'screening_has_forms' => [
            //         [
            //             'id' => null,
            //             'form_label' => '',
            //             'ordering' => 1
            //         ]
            //     ]
            // ],
            [
                'id' => null,
                'name' => 'Skrining PUMA',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'PUMA',
                        'ordering' => 1
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Persalinan',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'Partus',
                        'ordering' => 1
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Bayi baru Lahir',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'NeonatalEsensial',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'NewBornCheckUp',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'TTDExamination',
                        'ordering' => 3
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'TB Paru',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'TBContactHistory',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'TBRiskFactor',
                        'ordering' => 2
                    ],
                    [
                        'id' => null,
                        'form_label' => 'TBContactHistory',
                        'ordering' => 3
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Malaria',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'Malaria',
                        'ordering' => 1
                    ]
                ]
            ],
            // [
            //     'id' => null,
            //     'name' => 'Hipertensi',
            //     'ordering' => null,
            //     'screening_has_forms' => [
            //         [
            //             'id' => null,
            //             'form_label' => '',
            //             'ordering' => 1
            //         ]
            //     ]
            // ],
            [
                'id' => null,
                'name' => 'Imunisasi',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'ImmunizationHistory',
                        'ordering' => 1
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Skrining ICOPE',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'HearingLossHistory',
                        'ordering' => 1
                    ],
                    [
                        'id' => null,
                        'form_label' => 'VisualImpairmentTest',
                        'ordering' => 1
                    ]
                ]
            ],
            [
                'id' => null,
                'name' => 'Kespro Catin',
                'ordering' => null,
                'screening_has_forms' => [
                    [
                        'id' => null,
                        'form_label' => 'TetanusImmunization',
                        'ordering' => 1
                    ],
                    // [
                    //     'id' => null,
                    //     'form_label' => 'SRQ',
                    //     'ordering' => 1
                    // ]
                    [
                        'id' => null,
                        'form_label' => 'HemoglobinTest',
                        'ordering' => 2
                    ]
                ]
            ]
            // [
            //     'id' => null,
            //     'name' => 'Pasangan Usia Subur',
            //     'ordering' => null,
            //     'screening_has_forms' => [
            //         [
            //             'id' => null,
            //             'form_label' => '',
            //             'ordering' => 1
            //         ]
            //     ]
            // ],
        ];
        foreach ($arr as $data) {
            $screening = app(config('database.models.Screening'))->where('name',$data['name'])->first();
            if (!isset($screening)) {
                foreach ($data['screening_has_forms'] as &$screening_has_form) {
                    $form_model = app(config('database.models.Form'))->where('label',$screening_has_form['form_label'])->first();

                    $screening_has_form['form_id'] = $form_model->getKey();
                    unset($screening_has_form['form_label']);
                }
                app(config('app.contracts.Screening'))->prepareStoreScreening($this->requestDTO(ScreeningData::class,$data));
            }
        }
    }
}
