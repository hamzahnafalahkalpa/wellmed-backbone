<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleService\Models\Service;

class ClinicalPathologySeeder extends \Illuminate\Database\Seeder
{
    use HasRequestData;
    private $__service;
    protected $__datas = [
            [
                'name' => 'Pemeriksaan Darah',
                'childs' => [
                    [
                        'name' => 'Hematologi',
                        'childs' => [
                            ['name' => 'Hemoglobin (Hb)'],
                            ['name' => 'Hematokrit (Ht)'],
                            ['name' => 'Jumlah Eritrosit (RBC)'],
                            ['name' => 'Jumlah Leukosit (WBC)'],
                            ['name' => 'Hitung Trombosit (Platelet)'],
                            ['name' => 'LED (Laju Endap Darah)'],
                            ['name' => 'Hitung Jenis Leukosit (Diff Count)'],
                            ['name' => 'Retikulosit'],
                            ['name' => 'Indeks Eritrosit (MCV, MCH, MCHC)'],
                        ],
                    ],
                    [
                        'name' => 'Kimia Darah',
                        'childs' => [
                            [
                                'name' => 'Gula Darah',
                                'childs' => [
                                    ['name' => 'Gula Darah Puasa'],
                                    ['name' => 'Gula Darah 2 Jam PP'],
                                    ['name' => 'Gula Darah Sewaktu'],
                                    ['name' => 'HbA1c'],
                                ],
                            ],
                            [
                                'name' => 'Lipid Panel',
                                'childs' => [
                                    ['name' => 'Kolesterol Total'],
                                    ['name' => 'HDL Kolesterol'],
                                    ['name' => 'LDL Kolesterol'],
                                    ['name' => 'Trigliserida'],
                                ],
                            ],
                            [
                                'name' => 'Fungsi Ginjal',
                                'childs' => [
                                    ['name' => 'Kreatinin'],
                                    ['name' => 'Ureum'],
                                    ['name' => 'Asam Urat'],
                                ],
                            ],
                            [
                                'name' => 'Fungsi Hati',
                                'childs' => [
                                    ['name' => 'SGOT (AST)'],
                                    ['name' => 'SGPT (ALT)'],
                                    ['name' => 'Bilirubin Total'],
                                    ['name' => 'Bilirubin Direk'],
                                    ['name' => 'Albumin'],
                                    ['name' => 'Protein Total'],
                                ],
                            ],
                            [
                                'name' => 'Elektrolit',
                                'childs' => [
                                    ['name' => 'Natrium (Na)'],
                                    ['name' => 'Kalium (K)'],
                                    ['name' => 'Klorida (Cl)'],
                                    ['name' => 'Kalsium (Ca)'],
                                    ['name' => 'Fosfat'],
                                ],
                            ],
                            [
                                'name' => 'Enzim Jantung',
                                'childs' => [
                                    ['name' => 'CK-MB'],
                                    ['name' => 'Troponin I'],
                                    ['name' => 'Troponin T'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Imunologi & Serologi',
                'childs' => [
                    ['name' => 'Tes HIV (Anti HIV)'],
                    ['name' => 'HBsAg (Hepatitis B Surface Antigen)'],
                    ['name' => 'Anti-HBs'],
                    ['name' => 'HBeAg'],
                    ['name' => 'Anti-HCV (Hepatitis C Virus)'],
                    ['name' => 'Widal Test (Salmonella)'],
                    ['name' => 'Dengue NS1'],
                    ['name' => 'IgG/IgM Dengue'],
                    ['name' => 'Anti Toxoplasma IgG/IgM'],
                    ['name' => 'Anti Rubella IgG/IgM'],
                    ['name' => 'Anti CMV IgG/IgM'],
                    ['name' => 'Anti HSV IgG/IgM'],
                    ['name' => 'VDRL/RPR (Sifilis)'],
                ],
            ],
            [
                'name' => 'Hormon',
                'childs' => [
                    ['name' => 'TSH (Thyroid Stimulating Hormone)'],
                    ['name' => 'T3 Total'],
                    ['name' => 'T4 Total'],
                    ['name' => 'Free T3'],
                    ['name' => 'Free T4'],
                    ['name' => 'FSH (Follicle Stimulating Hormone)'],
                    ['name' => 'LH (Luteinizing Hormone)'],
                    ['name' => 'Prolaktin'],
                    ['name' => 'Progesteron'],
                    ['name' => 'Estradiol (E2)'],
                    ['name' => 'Testosteron'],
                ],
            ],
            [
                'name' => 'Mikrobiologi',
                'childs' => [
                    ['name' => 'Kultur Urin'],
                    ['name' => 'Kultur Sputum'],
                    ['name' => 'Kultur Darah'],
                    ['name' => 'Kultur Tinja'],
                    ['name' => 'Uji Sensitivitas Antibiotik (Antibiogram)'],
                ],
            ],
            [
                'name' => 'Urinalisa',
                'childs' => [
                    ['name' => 'Urin Lengkap'],
                    ['name' => 'Protein Urin 24 Jam'],
                    ['name' => 'Reduksi Gula Urin'],
                    ['name' => 'Sedimen Urin'],
                ],
            ],
            [
                'name' => 'Feses',
                'childs' => [
                    ['name' => 'Pemeriksaan Feses Lengkap'],
                    ['name' => 'Telur Cacing'],
                    ['name' => 'Darah Samar Feses (FOBT)'],
                    ['name' => 'Parasit Feses'],
                ],
            ],
            [
                'name' => 'Tumor Marker',
                'childs' => [
                    ['name' => 'CEA (Carcinoembryonic Antigen)'],
                    ['name' => 'AFP (Alpha Fetoprotein)'],
                    ['name' => 'PSA Total (Prostate Specific Antigen)'],
                    ['name' => 'CA-125 (Ovarium)'],
                    ['name' => 'CA-15-3 (Payudara)'],
                    ['name' => 'CA-19-9 (Pankreas)'],
                ],
            ],
            [
                'name' => 'Panel Khusus',
                'childs' => [
                    ['name' => 'Panel Pra Nikah'],
                    ['name' => 'Panel TORCH'],
                    ['name' => 'Panel Kesuburan'],
                    ['name' => 'Panel Check Up Dasar'],
                    ['name' => 'Panel Check Up Lengkap'],
                ],
            ],
        ];


    public function run()
    {
        $this->__service = app(config('database.models.Service', Service::class))->where('reference_type', 'MedicService')
                    ->whereLike('name','Patalogi Klinik')->first();
        if (isset($this->__service)){
            foreach ($this->__datas as $data) {
                app(config('app.contracts.ClinicalPathology'))->prepareStoreClinicalPathology(
                    $this->requestDTO(config('app.contracts.ClinicalPathologyData'), $this->prepare($data)));
            }
        }
    }

    private function prepare($data){
        $arr = [
            "id" => null,
            "name" => $data['name'],
            "medical_service_treatments" => [ //AUTOMATIC LABORATORIUM KLINIK
                [
                    "id"=> null,
                    "service_id" => $this->__service->getKey() //required, GET AUTOLIST - SERVICE LIST (MEDIC SERVICE - LABORATORIUM)
                ]
            ],
            "treatment" => [
                "id" => null,
                "service_label_id" => null, //nullable, GET FORM AUTOLIST - SERVICE LABEL LIST
                "status"=> "ACTIVE", //require, in=> ACTIVE, INACTIVE
            ],
            "childs" => [
                
            ]
        ];
        if (isset($data['childs']) && count($data['childs']) > 0){
            foreach ($data['childs'] as $child) {
                $arr['childs'][] = $this->prepare($child);
            }
        }
        return $arr;
    }
}