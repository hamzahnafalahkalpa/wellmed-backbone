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
    protected function afterVisitRegistrationCreated(Model &$visit_registration_model, VisitRegistrationData &$visit_registration_dto): self
    {
        parent::afterVisitRegistrationCreated($visit_registration_model, $visit_registration_dto);

        $visit_patient_model = $visit_registration_model->visitPatient;
        $visit_registration_model->encounter_code ??= Str::orderedUuid()->toString();
        $visit_registration_model->ihs_number ??= null;
        $visit_registration_model->save();

        if (!isset($visit_registration_model->ihs_number)) {
            $patient_model = $visit_registration_dto->patient_model ?? $visit_patient_model->patient ?? null;

            if (isset($patient_model)) {
                $payload = $this->prepareSatuSehatEncounterPayload($visit_registration_model, $visit_patient_model, $patient_model);
                $this->dispatchSatuSehatSync($visit_registration_model, $patient_model, $payload);
            }
        }
        return $this;
    }

    /**
     * Prepare the payload for Satu Sehat encounter integration.
     */
    private function prepareSatuSehatEncounterPayload(Model $visit_registration_model, Model $visit_patient_model, Model $patient_model): array
    {
        $medic_service = $visit_registration_model->medicService;
        $room = $this->RoomModel()->where('medic_service_id', $medic_service->getKey())->whereNotNull("props->ihs_number")->first();
        $period = $visit_registration_model->created_at->format('Y-m-d H:i:s');

        return [
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
            // 'organization_code' => config('satu-sehat.client_organization_id') ?? config('satu-sehat.organization_id'),
            'organization_code' => config('satu-sehat.organization_id'),
            'visit_code' => $visit_patient_model->visit_code ?? Str::orderedUuid()->toString(),
            'period' => $period,
            'status_history' => [
                'arrived' => $period
            ],
            // 'location_code' => '3d44d9ed-618f-45e6-b605-b36bc21ef3a5',
            'location_code' => $room->ihs_number,
            'location_name' => 'Poli Umum'
        ];
    }

    /**
     * Dispatch encounter data to Satu Sehat via async job if enabled.
     */
    private function dispatchSatuSehatSync(Model $visit_registration_model, Model $patient_model, array $payload): void
    {
        if (!config('module-patient.satu-sehat.enable', true)) {
            return;
        }

        $tenant_id = tenancy()->tenant->getKey();
        $visit_registration_id = $visit_registration_model->getKey();
        $patient_id = $patient_model->getKey();

        try {
            dispatch(new SendEncounterToSatuSehatJob(
                $tenant_id,
                $visit_registration_id,
                $patient_id,
                $payload
            ))->onQueue('satusehat')->onConnection(config('queue.default', 'rabbitmq'));

            Log::channel('satu-sehat')->info('Encounter queued for Satu Sehat sync', [
                'visit_registration_id' => $visit_registration_id,
                'patient_id' => $patient_id,
                'tenant_id' => $tenant_id
            ]);
        } catch (\Throwable $exception) {
            Log::channel('satu-sehat')->error('Failed to queue encounter for Satu Sehat', [
                'visit_registration_id' => $visit_registration_id,
                'patient_id' => $patient_id,
                'error' => $exception->getMessage()
            ]);
        }
    }
}
