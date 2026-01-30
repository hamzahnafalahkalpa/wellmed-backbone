<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData;
use Hanafalah\ModulePatient\Schemas\VisitRegistration as SchemasVisitRegistration;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\VisitRegistration as ModulePatientVisitRegistration;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\{
    Model
};
use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Jobs\SendEncounterToSatuSehatJob;
use Hanafalah\MicroTenant\Facades\MicroTenant;

class VisitRegistration extends SchemasVisitRegistration implements ModulePatientVisitRegistration
{
    protected function afterVisitRegistrationCreated(Model &$visit_registration_model, VisitRegistrationData &$visit_registration_dto): self{
        parent::afterVisitRegistrationCreated($visit_registration_model, $visit_registration_dto);
        $visit_patient_model = $visit_registration_model->visitPatient;
        $visit_registration_model->encounter_code ??= Str::orderedUuid()->toString();
        $visit_registration_model->ihs_number ??= null;
        if (!isset($visit_registration_model->ihs_number)){
            $patient_model = $visit_registration_dto->patient_model ?? $visit_patient_model->patient ?? null;
            if (isset($patient_model)){
                // $workspace = tenancy()->tenant->reference;
                try {
                    $medic_service = $visit_registration_model->medicService;
                    $room = $this->RoomModel()->where('medic_service_id',$medic_service->getKey())->whereNotNull("props->ihs_number")->first();
                    $form_payload = [
                        'encounter_code' => $visit_registration_model->encounter_code,
                        'status' => 'arrived',
                        'class_code' => 'AMB',
                        'patient_code' => $patient_model->prop_card_identity['ihs_number'] ?? null,
                        'patient_name' => $patient_model->name,
                        'participant' => [
                            'attenders' => [
                                [
                                    "participant_code" => "12778338166",
                                    "participant_name" => "MULJADIE SETIAWAN"
                                ]
                            ]
                        ],
                        // 'organization_code' => $workspace->integration['satu_sehat']['general']['ihs_number'] ?? null,
                        'organization_code' => config('satu-sehat.organization_id'),
                        'visit_code' => $visit_patient_model->visit_code ?? Str::orderedUuid()->toString(),
                        'period' => $period = $visit_registration_model->created_at->format('Y-m-d H:i:s'),
                        'status_history' => [
                            'arrived' => $period
                        ],
                        'location_code' => $room->ihs_number ?? null, //3d44d9ed-618f-45e6-b605-b36bc21ef3a5
                        'location_code' => '3d44d9ed-618f-45e6-b605-b36bc21ef3a5',
                        // 'location_name' => $room->ihs_name ?? $room->name." - ".$room->getKey()
                        // 'location_name' => $room->ihs_name ?? $room->name." - ".$room->getKey()
                        'location_name' => 'Poli Umum'
                    ];

                    // Dispatch job to send encounter data to Satu Sehat asynchronously via RabbitMQ
                    SendEncounterToSatuSehatJob::dispatch(
                        tenancy()->tenant->getKey(),
                        $visit_registration_model->getKey(),
                        $patient_model->getKey(),
                        $form_payload
                    );

                    Log::channel('satu-sehat')->info("Encounter queued for Satu Sehat sync", [
                        'visit_registration_id' => $visit_registration_model->getKey(),
                        'patient_id' => $patient_model->getKey(),
                        'tenant_id' => tenancy()->tenant->getKey()
                    ]);
                } catch (\Throwable $th) {
                    Log::channel('satu-sehat')->error("Failed to queue encounter for Satu Sehat", [
                        'visit_registration_id' => $visit_registration_model->getKey() ?? null,
                        'patient_id' => $patient_model->getKey() ?? null,
                        'error' => $th->getMessage()
                    ]);
                }
            }
        }
        $visit_registration_model->save();
        return $this;
    }
}
