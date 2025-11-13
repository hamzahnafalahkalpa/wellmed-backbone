<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleExamination\Models\Form\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder{
    use HasRequestData;

    private $__form;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->__form = app(config('database.models.Form', Form::class));

        $forms = [
            // $this->modelMorph('AdministrationVitaminA') => [
            //     'name'    => 'Pemberian Vitamin A',
            //     'ordering' => 1,
            //     'type'    => 'Plan'
            // ],
            $this->modelMorph('Allergy') => [
                'name' => 'Riwayat Alergi Pasien',
                'ordering' => 4,
                'examination_key' => 'assessment',
                'type' => 'Subjective'
            ],
            // $this->modelMorph('Alloanamnese') => [
            //     'name' => 'Alloanamnese',
            //     'ordering' => 1,
            //     'examination_key' => 'assessment',
            //     'type' => 'Subjective'
            // ],
            // $this->modelMorph('ANCTerpadu') => [
            //     'name'    => 'ANC TERPADU',
            //     'examination_key' => 'assessment',
            //     'type'    => 'Subjective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/ANCTerpadu.php')
            //     ]
            // ],
            $this->modelMorph('Anthropometry') => [
                'name' => 'Anthropometry',
                'ordering' => 3,
                'examination_key' => 'assessment',
                'type'    => 'Objective'
            ],
            // $this->modelMorph('AudiometriTest') => [
            //     'name' => 'Tes Audiometri',
            //     'ordering' => 3,
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('BloodSugarTest') => [
            //     'name' => 'Tes Gula Darah',
            //     'ordering' => 3,
            //     'type'    => 'Plan'
            // ],
            // $this->modelMorph('ChildAndPregnancyHistory') => [
            //     'name' => 'Anak dan Riwayat Persalinan',
            // 'examination_key' => 'assessment',
            //     'type'    => 'Subjective'
            // ],
            // $this->modelMorph('ChildGrowth') => [
            //     'name' => 'Tumbuh Kembang Anak',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/ChildGrowth.php')
            //     ]
            // ],
            // $this->modelMorph('EarExamination') => [
            //     'name' => 'Pemeriksaan Telinga',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('EyeExamination') => [
            //     "name" => "Pemeriksaan Mata Lengkap",
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('EyeRefractionExamination') => [
            //     "name" => "Refraksi Mata",
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('EyeVisionColor') => [
            //     "name" => "Pemeriksaan matan dan Color Vision",
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('FamilyPlanningService') => [
            //     "name" => "Pelayanan KB",
            //     'type'    => 'Plan'
            // ],
            // $this->modelMorph('Partus') => [
            //     "name" => "Persalinan",
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/Partus.php')
            //     ]
            // ],
            // $this->modelMorph('HearingFunction') => [
            //     'name'    => 'PEMERIKSAAN FUNGSI PENDENGARAN',
                // 'examination_key' => 'assessment',
            //     'type' => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/HearingFunction.php')
            //     ]
            // ],
            // $this->modelMorph('HearingLossHistory') => [
            //     'name' => 'Gangguan Pendengaran',
                // 'examination_key' => 'assessment',
            //     'type' => 'Objective'
            // ],
            // $this->modelMorph('HemoglobinTest') => [
            //     "name" => "Pemeriksaan Hemoglobin (HB)",
                // 'examination_key' => 'assessment',
            //     'type' => 'Objective'
            // ],
            // $this->modelMorph('ImmunizationHistory') => [
            //     "name" => "Riwayat Imunisasi",
                // 'examination_key' => 'assessment',
            //     'type' => 'Objective'
            // ],
            // $this->modelMorph('LarynxExamination') => [
            //     'name' => 'Pemeriksaan Laring',
                // 'examination_key' => 'assessment',
            //     'type' => 'Objective'
            // ],
            // $this->modelMorph('MouthCavity') => [
            //     'name'    => 'KEADAAN RONGGA MULUT',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/MouthCavity.php')
            //     ]
            // ],
            // $this->modelMorph('MouthCavityOther') => [
            //     'name'    => 'KEADAAN GUSI, KEBERSIHAN GIGI,DAN KONDISI LAINNYA',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/MouthCavityOther.php')
            //     ]
            // ],
            // $this->modelMorph('NeonatalEsensial') => [
            //     "name" => "Neonatal Esential",
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/NeonatalEsensial.php')
            //     ] 
            // ],
            // $this->modelMorph('NewBornCheckUp') => [
            //     'name'    => 'PEMERIKSAAN BAYI BARU LAHIR',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/NewBornCheckUp.php')
            //     ]
            // ],
            // $this->modelMorph('NoseExamination') => [
            //     'name' => 'Pemeriksaan Hidung',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('Odontogram') => [
            //     'name' => 'Odontogram',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('POPMHistory') => [
            //     "name" => "Pemberian obat pencegahan massal cacingan (POPM)",
                // 'examination_key' => 'assessment',
            //     'type' => 'Objective'
            // ],
            $this->modelMorph('PainScale') => [
                'name' => 'Sekala Nyeri',
                'examination_key' => 'assessment',
                'type'    => 'Objective',
                'ordering' => 2
            ],
            // $this->modelMorph('PostpartumObservation') => [
            //     'name'    => 'PELAYANAN KESEHATAN IBU NIFAS',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/PostpartumObservation.php')
            //     ]
            // ],
            $this->modelMorph('Symptom') => [
                'name' => 'Gejala & Keluhan',
                'examination_key' => 'assessment',
                'type'    => 'Subjective',
                'ordering' => 2
            ],
            // $this->modelMorph('TTDExamination') => [
            //     "name" => "Tablet Tambah Darah (TTD)",
            // 'examination_key' => 'treatments',
            //     'type'    => 'Assessment'
            // ],
            // $this->modelMorph('TetanusImmunization') => [
            //     "name" => "Riwayat Imunisasi Tetanus",
            //     'examination_key' => 'assessment',
            //     'type'    => 'Subjective'
            // ],
            // $this->modelMorph('ThoraxExamination') => [
            //     'name'    => 'PEMERIKSAAN FOTO THORAX',
            //     'examination_key' => 'assessment',
            //     'type'    => 'Objective',
            //     'form_has_survey' => [
            //         "survey" => include(__DIR__ . '/data/forms/ThoraxExamination.php')
            //     ]
            // ],
            // $this->modelMorph('ThroatExamination') => [
            //     'name' => 'Pemeriksaan Tenggorokan',
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            // $this->modelMorph('VisualImpairmentTest') => [
            //     "name" => "Formulir Gangguan Pengelihatan",
                // 'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            $this->modelMorph('VitalSign') => [
                'name' => 'Tanda Tanda Vital',
                'examination_key' => 'assessment',
                'type'    => 'Objective',
                'ordering' => 1
            ],
            // $this->modelMorph('InitialDiagnose') => [
            //    'name'  => 'Diagnosa Awal/Masuk',
            //     'examination_key' => 'assessment',
            //    'type'    => 'Objective'
            // ],
            $this->modelMorph('BasicDiagnose') => [
                'name'  => 'Diagnosa',
                'examination_key' => 'assessment',
                'type'    => 'Objective',
                'ordering' => 8
            ],
            // $this->modelMorph('PrimaryDiagnose') => [
            //     'name'  => 'Diagnosa Primer',
            //     'examination_key' => 'assessment',
            //    'type'    => 'Objective'
            // ],
            // $this->modelMorph('SecondaryDiagnose') => [
            //     'name'  => 'Diagnosa Sekunder',
            //     'examination_key' => 'assessment',
            //     'type'    => 'Objective'
            // ],
            $this->modelMorph('PatientFamilyIllness') => [
                'name' => 'Riwayat Penyakit Patient',
                'examination_key' => 'assessment',
                'type'    => 'Subjective',
                'ordering' => 4
            ],
            // $this->modelMorph('HistoryIllness') => [
            //     'name' => 'Riwayat Penyakit Patient',
            //     'examination_key' => 'assessment',
            //     'type'    => 'Subjective'
            // ],
            // $this->modelMorph('FamilyIllness') => [
            //     'name' => 'Riwayat Penyakit Keluarga Patient',
            //     'examination_key' => 'assessment',
            //     'type'    => 'Subjective'
            // ],
            $this->modelMorph('PhysicalExamination') => [
                'name' => 'Pemeriksaan Fisik',
                'examination_key' => 'assessment',
                'type'    => 'Objective',
                'ordering' => 5
            ],
            $this->modelMorph('ClinicalTreatment') => [
                'name' => 'Tindakan Medis Poliklinik',
                'examination_key' => 'treatments',
                'type'    => 'Assessment'
            ],
            $this->modelMorph('BasicPrescription') => [
                'name' => 'Resep Obat',
                'examination_key' => 'prescriptions',
                'type'    => 'Plan'
            ],
            $this->modelMorph('SubjectNote') => [
                'name' => 'Catatan Subjektif',
                'examination_key' => 'assessment',
                'type'    => 'Subjective',
                'ordering' => 99
            ],
            $this->modelMorph('ObjectNote') => [
                'name' => 'Catatan Objektif',
                'examination_key' => 'assessment',
                'type'    => 'Objective',
                'ordering' => 99
            ],
            $this->modelMorph('PlanNote') => [
                'name' => 'Catatan Subjektif',
                'examination_key' => 'assessment',
                'type'    => 'Plan',
                'ordering' => 99
            ],
            $this->modelMorph('AssessmentNote') => [
                'name' => 'Catatan Assessment',
                'examination_key' => 'assessment',
                'type'    => 'Assessment',
                'ordering' => 99
            ]
            // $this->modelMorph('Prescription') => [
            //     'name' => 'Resep Obat',
            //     'examination_key' => 'prescriptions',
            //     'type'    => 'Plan'
            // ],
            // $this->modelMorph('MedicToolPrescription') => [
            //     'name' => 'Resep BMHP',
            //     'examination_key' => 'prescriptions',
            //     'type'    => 'Plan'
            // ],
            // $this->modelMorph('MixPrescription') => [
            //     'name' => 'Resep Racik',
            //     'examination_key' => 'prescriptions',
            //     'type'    => 'Plan'
            // ]
        ];
        $this->createForm($forms);
    }

    private function model(string $model): Model{
        return app(config('database.models.'.$model));
    }

    private function modelMorph(string $model): string{
        try {
            return $this->model($model)->getMorphClass();
        } catch (\Throwable $th) {
        }
    }

    private function createForm($forms,$parent_id=null){
        foreach ($forms as $label => $form) {
            $form['label'] = $label;
            try {
                $form_model = app(config('app.contracts.Form'))->prepareStoreForm(
                    $this->requestDTO(config('app.contracts.FormData'), $form),
                );
            } catch (\Throwable $th) {
                throw $th;
            }

            // $form_model = $this->__form->updateOrCreate([
            //     'parent_id' => $parent_id,
            //     'label'     => $label,
            //     'name'      => $form['name']
            // ]);

            // if (isset($form['prop']) && count($form['prop']) > 0){
            //     foreach ($form['prop'] as $prop_key => $prop) $form_model->{$prop_key} = $prop;
            //     $form_model->save();
            // }

            // if (isset($form['childs']) && count($form['childs']) > 0){
            //     $this->createForm($form['childs'],$form_model->getKey());
            // }

            // if (isset($surveys)){
            //     $surveys = $form['form_has_survey'] ?? [];
            //     if (!is_array($surveys)) $surveys = [$surveys];

            //     $attaches = [];
            //     foreach ($surveys as $survey) {
            //         $survey_model = $this->model('Survey')->updateOrCreate([
            //             'flag' => $survey['flag']
            //         ],[
            //             'name' => $survey['name']
            //         ]);

            //         if (isset($survey['props'])){
            //             $dynamic_forms = [];
            //             foreach ($survey['props']['dynamic_forms'] as $key => $dynamic_form){
            //                 $dynamic_form['ordering'] = $key + 1;
            //                 $dynamic_forms[] = $dynamic_form;
            //             }
            //             $survey_model->setAttribute('dynamic_forms', $dynamic_forms);
            //             $survey_model->save();
            //         }
            //         $attaches[] = $survey_model;
            //     }
            //     $form_model->surveys()->sync($attaches);
            // }
        }
    }
}
