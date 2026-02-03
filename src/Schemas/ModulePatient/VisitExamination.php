<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Schemas\VisitExamination as SchemasVisitExamination;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\VisitExamination as ModulePatientVisitExamination;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\{
    Model
};
use Projects\WellmedBackbone\Jobs\SendObservationToSatuSehatJob;
use Projects\WellmedBackbone\Services\DashboardMetricsService;

class VisitExamination extends SchemasVisitExamination implements ModulePatientVisitExamination
{
    public function prepareVisitExaminationSignOff(Model &$visit_examination_model, VisitExaminationData &$visit_examination_dto): Model
    {
        $visit_examination = parent::prepareVisitExaminationSignOff($visit_examination_model, $visit_examination_dto);
        $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $visit_examination->visitRegistration;
        $patient_model = $visit_examination_dto->patient_model ?? $visit_examination_model->patient ?? null;

        if (isset($patient_model)) {
            $payload = $this->prepareSatuSehatObservationPayload($visit_examination, $visit_registration_model, $patient_model);
            $this->dispatchSatuSehatSync($visit_examination_model, $patient_model, $payload);
        }
        if ($this->is_recently_created){
            $this->updateDashboardStatistics($visit_examination,'unsigned');
        }

        if ($this->is_sign_off){
            if ($visit_examination->treatments()->count() > 0){
                $this->updateDashboardStatistics($visit_examination,'treatment');
            }
            $this->updateDashboardStatistics($visit_examination,'unsigned-decrement');
        }

        return $visit_examination;
    }

    /**
     * Update dashboard statistics when a new patient is created.
     * Updates both total patients count and new patients count.
     *
     * @param Model $patient
     * @return void
     */
    private function updateDashboardStatistics(Model $visit_examination,string $type): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(DashboardMetricsService::class);

            switch ($type) {
                case 'treatment': $dashboardService->incrementNewTreatment();break;
                case 'unsigned': $dashboardService->incrementNewUnsignedVisit();break;
                case 'unsigned-decrement': $dashboardService->decrementNewUnsignedVisit();break;
            }

            Log::channel('elasticsearch')->info('Dashboard statistics updated for new visit_examination', [
                'visit_examination_id' => $visit_examination->getKey()
            ]);

        } catch (\Throwable $e) {
            // Don't fail visit_examination creation if dashboard update fails
            Log::channel('elasticsearch')->error('Failed to update dashboard statistics', [
                'visit_examination_id' => $visit_examination->getKey(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Prepare the payload for Satu Sehat observation integration.
     */
    private function prepareSatuSehatObservationPayload(Model $visit_examination, Model $visit_registration_model, Model $patient_model): array
    {
        $vital_sign = $this->VitalSignModel()->where('morph', 'VitalSign')->where('examination_id', $visit_examination->getKey())->first();
        $anthro = $this->AnthropometryModel()->where('morph', 'Anthropometry')->where('examination_id', $visit_examination->getKey())->first();
        $vital_exam = isset($vital_sign) ? $vital_sign->exam ?? [] : [];
        $antrho_exam = isset($anthro) ? $anthro->exam ?? [] : [];

        return [
            "encounter_code" => $visit_registration_model->ihs_number,
            "encounter_display" => "Pemeriksaan Pasien " . $patient_model->name,
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
    }

    /**
     * Dispatch observation data to Satu Sehat via async job if enabled.
     */
    private function dispatchSatuSehatSync(Model $visit_examination_model, Model $patient_model, array $payload): void
    {
        if (!config('module-patient.satu-sehat.enable', true)) {
            return;
        }

        $tenant_id = tenancy()->tenant->getKey();
        $visit_examination_id = $visit_examination_model->getKey();
        $patient_id = $patient_model->getKey();

        try {
            dispatch(new SendObservationToSatuSehatJob(
                $tenant_id,
                $visit_examination_id,
                $patient_id,
                $payload
            ))->onQueue('satusehat')->onConnection(config('queue.default', 'rabbitmq'));

            Log::channel('satu-sehat')->info('Observation queued for Satu Sehat sync', [
                'visit_examination_id' => $visit_examination_id,
                'patient_id' => $patient_id,
                'tenant_id' => $tenant_id
            ]);
        } catch (\Throwable $exception) {
            Log::channel('satu-sehat')->error('Failed to queue observation for Satu Sehat', [
                'visit_examination_id' => $visit_examination_id,
                'patient_id' => $patient_id,
                'error' => $exception->getMessage()
            ]);
        }
    }
}
