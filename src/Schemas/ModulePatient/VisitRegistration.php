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
use Projects\WellmedBackbone\Services\DashboardMetricsService;
use Projects\WellmedBackbone\Schemas\Concerns\HasSatuSehatDashboard;

class VisitRegistration extends SchemasVisitRegistration implements ModulePatientVisitRegistration
{
    use HasSatuSehatDashboard;

    protected function afterVisitRegistrationCreated(Model &$visit_registration_model, VisitRegistrationData &$visit_registration_dto): self
    {
        parent::afterVisitRegistrationCreated($visit_registration_model, $visit_registration_dto);

        $visit_patient_model = $visit_registration_model->visitPatient;
        $visit_registration_model->encounter_code ??= Str::orderedUuid()->toString();
        $visit_registration_model->ihs_number ??= null;
        $visit_registration_model->save();

        if ($this->is_recently_created){
            $this->updateDashboardStatistics($visit_registration_model,'trends');
            // $this->updateDashboardStatistics($visit_registration_model,'queue-service');
            // Increment wellMedCount for Satu Sehat dashboard
            $this->incrementSatuSehatWellMedCount('encounter');
        }

        if (!isset($visit_registration_model->ihs_number)) {
            $patient_model = $visit_registration_dto->patient_model ?? $visit_patient_model->patient ?? null;

            if (isset($patient_model)) {
                $this->prepareStoreEncounterSatuSehatLog($visit_registration_model,$visit_patient_model,$patient_model);
            }
        }
        return $this;
    }

    public function prepareStoreEncounterSatuSehatLog(Model $visit_registration_model, Model $visit_patient_model, Model $patient_model, ?array $payload = null, ? array $existing = null){
        $payload ??= $this->prepareSatuSehatEncounterPayload($visit_registration_model, $visit_patient_model, $patient_model);
        $this->dispatchSatuSehatSync($visit_registration_model, $patient_model, $payload, $existing);
    }

    /**
     * Prepare the payload for Satu Sehat encounter integration.
     */
    private function prepareSatuSehatEncounterPayload(Model $visit_registration_model, Model $visit_patient_model, Model $patient_model): array
    {
        $medic_service = $visit_registration_model->medicService;
        $room = $this->RoomModel()->where('medic_service_id', $medic_service->getKey())->whereNotNull("props->ihs_number")->first();
        $period = $visit_registration_model->created_at->format('Y-m-d H:i:s');
        if (isset($visit_registration_model->practitionerEvaluation)){
            $practitioner = $visit_registration_model->practitionerEvaluation->practitioner;
        }
        $encounter_payload = [
            'encounter_code' => $visit_registration_model->encounter_code,
            'status' => 'arrived',
            'class_code' => 'AMB',
            'patient_code' => $patient_model->prop_card_identity['ihs_number'] ?? null,
            'patient_name' => $patient_model->name,
            // 'participant' => [
            //     'attenders' => [
            //         // [
            //         //     "participant_code" => $practitioner?->prop_card_identity['ihs_number'] ?? null,
            //         //     "participant_name" => $practitioner->name    
            //         // ]
            //         [
            //             "participant_code" => "12778338166",
            //             "participant_name" => "MULJADIE SETIAWAN"
            //         ]
            //     ]
            // ],
            // 'organization_code' => config('satu-sehat.client_organization_id') ?? config('satu-sehat.organization_id'),
            'organization_code' => config('satu-sehat.organization_id'),
            'visit_code' => $visit_patient_model->visit_code ?? Str::orderedUuid()->toString(),
            'period' => $period,
            'status_history' => [
                'arrived' => $period
            ],
            // 'location_code' => '3d44d9ed-618f-45e6-b605-b36bc21ef3a5',
            'location_code' => $room->ihs_number ?? null,
            'location_name' => 'Poli Umum'
        ];
        if (isset($practitioner)){
            $encounter_payload['participant'] = [
                'attenders' => [
                    [
                        "participant_code" => $practitioner?->prop_card_identity['ihs_number'] ?? null,
                        "participant_name" => $practitioner->name    
                    ]
                ]
            ];
        }
        return $encounter_payload;
    }

    /**
     * Dispatch encounter data to Satu Sehat via async job if enabled.
     */
    private function dispatchSatuSehatSync(Model $visit_registration_model, Model $patient_model, array $payload,? array $existing = null): void
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
                $payload,
                $existing['id'] ?? null,
                $existing['referenceId'] ?? null,
                $existing['referenceType'] ?? null
            ))->onQueue('satusehat')->onConnection(config('queue.default', 'rabbitmq'));
            // ))->onQueue('satusehat')->onConnection('sync');

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

    /**
     * Update dashboard statistics when a new visit registration is created.
     *
     * @param Model $patient
     * @return void
     */
    private function updateDashboardStatistics(Model $visit_registration,string $type,?array $data = []): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(DashboardMetricsService::class);
            switch ($type) {
                case 'queue-service' :
                    $dashboardService->incrementNewQueueService([
                        'id' => $visit_registration->getKey(),
                        'service' => $visit_registration->prop_medic_service['name'] ?? $visit_registration->medicService->name,
                        'service_label' => $visit_registration->prop_medic_service['label'] ?? $visit_registration->medicService->label,
                    ]);
                break;
                case 'trends' :
                    $dashboardService->incrementTrend([
                        'service' => $visit_registration->prop_medic_service['name'] ?? $visit_registration->medicService->name,
                        'service_label' => $visit_registration->prop_medic_service['label'] ?? $visit_registration->medicService->label,
                    ]);
                break;
            }

            Log::channel('elasticsearch')->info('Dashboard statistics updated for new visit_registration', [
                'visit_registration_id' => $visit_registration->getKey()
            ]);

        } catch (\Throwable $e) {
            // Don't fail visit_registration creation if dashboard update fails
            Log::channel('elasticsearch')->error('Failed to update dashboard statistics', [
                'visit_registration_id' => $visit_registration->getKey(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
