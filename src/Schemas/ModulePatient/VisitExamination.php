<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Schemas\VisitExamination as SchemasVisitExamination;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\VisitExamination as ModulePatientVisitExamination;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\{
    Model
};

class VisitExamination extends SchemasVisitExamination implements ModulePatientVisitExamination
{
    public function prepareVisitExaminationSignOff(Model &$visit_examination_model, VisitExaminationData &$visit_examination_dto): Model{
        $visit_examination = parent::prepareVisitExaminationSignOff($visit_examination_model, $visit_examination_dto);
        $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $visit_examination->visitRegistration;
        $patient_model = $visit_examination_dto->patient_model ?? $visit_examination_model->patient ?? null;
        if (isset($patient_model)){
            $vital_sign = $this->VitalSignModel()->where('morph','VitalSign')->where('examination_id',$visit_examination->getKey())->first();
            $anthro = $this->AnthropometryModel()->where('morph','Anthropometry')->where('examination_id',$visit_examination->getKey())->first();
            $vital_exam = isset($vital_sign) ? $vital_sign->exam ?? [] : [];
            $antrho_exam = isset($anthro) ? $anthro->exam ?? [] : [];
            $form_payload = [
                "encounter_code" => $visit_registration_model->ihs_number,
                "encounter_display" => "Pemeriksaan Pasien ".$patient_model->name,
                "status" => "final",
                "patient_code" => $patient_model->prop_card_identity['ihs_number'] ?? null,
                "organization_code" => config('satu-sehat.organization_id'),
                "practitioner_codes" => [
                    "10006926841"
                ],
                "issued_at" => now()->format('Y-m-d H:i:s'),
                "category" => [
                    "vital_signs" => [
                        "heart_rate" => $vital_exam['heart_rate'] ?? null,
                        "oxygen_saturation" => $vital_exam['oxygen_saturation'] ?? null,
                        "respiratory_rate" => $vital_exam['respiration_rate'] ?? null,
                        "body_temperature" => $vital_exam['temperature'] ?? null,
                        "body_height" => $antrho_exam['height'] ?? null,
                        "body_weight" => $antrho_exam['weight'] ?? null,
                        "body_mass_index" => $antrho_exam['bmi'] ?? null,
                        "systolic_blood_pressure" => $vital_exam['systolic'] ?? null,
                        "diastolic_blood_pressure" => $vital_exam['diastolic'] ?? null
                    ]
                ]
            ];
            try {
                $observation_satu_sehat = $this->schemaContract('observation_satu_sehat')->useAccessToSatuSehat()
                    ->prepareStoreObservationSatuSehat(
                    $this->requestDTO(
                        config('app.contracts.ObservationSatuSehatData'),[
                            'model' => $visit_examination_model,
                            'form'  => $form_payload
                        ]
                    )
                );
                // $visit_examination_model->ihs_number = $observation_satu_sehat->response['id'] ?? null;
            } catch (\Throwable $th) {
                Log::channel('satu-sehat')->error($th->getMessage());
            }
        }
        return $visit_examination;
    }
}
