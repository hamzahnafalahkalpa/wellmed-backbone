<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;
use Projects\WellmedBackbone\Jobs\JobRequest;

class DocumentTypeSeeder extends Seeder{
    use HasRequestData;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";
        $datas = $this->getDocumentTypes();
        foreach ($datas as $data) {
            app(config('app.contracts.DocumentType'))->prepareStoreDocumentType(
                $this->requestDTO(config('app.contracts.DocumentTypeData'), $data)
            );
        }
    }

    private function getDocumentTypes(): array{
        return [
            [
                'label' => 'INFORMED CONSENT',
                'name'  => 'Informed Consent',
                'dynamic_forms' => [],
                'childs' => [
                    [
                        'label' => 'GENERAL CONSENT',
                        'name'  => 'General Consent',
                        'dynamic_forms' => [
                            [
                                'label'          => 'Nama Wali/Penanggung Jawab',
                                'key'            => 'guardian_name',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => null,
                                'options'        => []
                            ],
                            [
                                'label'          => 'Hubungan dengan Pasien',
                                'key'            => 'relation_to_patient',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => null,
                                'options'        => []
                            ],
                            [
                                'label'          => 'Tanggal Persetujuan',
                                'key'            => 'consent_date',
                                'type'           => 'DATE',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ]
                        ]
                    ],
                    [
                        'label' => 'ACCEPTANCE TREATMENT CONSENT',
                        'name'  => 'Persetujuan Tindakan Medis',
                        'dynamic_forms' => [
                            [
                                'label'          => 'Tindakan Medis',
                                'key'            => 'treatment',
                                'type'           => 'MultiSelect',
                                'component_name' => 'MultiSelectTreatment',
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ],
                            [
                                'label'          => 'Nama Wali/Penanggung Jawab',
                                'key'            => 'guardian_name',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => null,
                                'options'        => []
                            ],
                            [
                                'label'          => 'Hubungan dengan Pasien',
                                'key'            => 'relation_to_patient',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => null,
                                'options'        => []
                            ],
                            [
                                'label'          => 'Tanggal Persetujuan',
                                'key'            => 'consent_date',
                                'type'           => 'DATE',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ]
                        ]
                    ],
                    [
                        'label' => 'DECLINE TREATMENT CONSENT',
                        'name'  => 'Penolakan Tindakan Medis',
                        'dynamic_forms' => [
                            [
                                'label'          => 'Tindakan Medis',
                                'key'            => 'treatment',
                                'type'           => 'MultiSelect',
                                'component_name' => 'MultiSelectTreatment',
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ],
                            [
                                'label'          => 'Alasan Penolakan',
                                'key'            => 'reason',
                                'type'           => 'TEXTAREA',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ],
                            [
                                'label'          => 'Nama Wali/Penanggung Jawab',
                                'key'            => 'guardian_name',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => null,
                                'options'        => []
                            ],
                            [
                                'label'          => 'Hubungan dengan Pasien',
                                'key'            => 'relation_to_patient',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => null,
                                'options'        => []
                            ],
                            [
                                'label'          => 'Tanggal Persetujuan',
                                'key'            => 'consent_date',
                                'type'           => 'DATE',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ]
                        ]
                    ]
                ]
            ],
            [
                'label' => 'MEDICAL CERTIFICATE',
                'name'  => 'Medical Certificate',
                'dynamic_forms' => [],
                'childs' => [
                    [
                        'label' => 'FIT TO WORK CERTIFICATE',
                        'name'  => 'Surat Keterangan Sehat untuk Bekerja',
                        'dynamic_forms' => [

                        ]
                    ],
                    [
                        'label' => 'SICK LEAVE CERTIFICATE',
                        'name'  => 'Surat Keterangan Sakit',
                        'dynamic_forms' => [
                            [
                                'label'          => 'Lama Istirahat',
                                'key'            => 'rest_duration',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ]
                        ]
                    ],
                    [
                        'label' => 'DISABILITY CERTIFICATE',
                        'name'  => 'Surat Keterangan Cacat',
                        'dynamic_forms' => [
                            [
                                'label'          => 'Jenis Cacat',
                                'key'            => 'disability_type',
                                'type'           => 'INPUT',
                                'component_name' => null,
                                'default_value'  => null,
                                'attribute'      => null,
                                'rule'           => 'required',
                                'options'        => []
                            ]
                        ]
                    ],
                    [
                        'label' => 'PREGNANCY CERTIFICATE',
                        'name'  => 'Surat Keterangan Hamil'
                    ],
                    [
                        'label' => 'NON-PREGNANCY CERTIFICATE',
                        'name'  => 'Surat Keterangan Tidak Hamil'
                    ]
                ]
            ]
        ];
    }
}
