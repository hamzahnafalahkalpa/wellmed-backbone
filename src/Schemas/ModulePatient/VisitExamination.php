<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Schemas\VisitExamination as SchemasVisitExamination;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\VisitExamination as ModulePatientVisitExamination;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Jobs\SendObservationToSatuSehatJob;
use Projects\WellmedBackbone\Services\DashboardMetricsService;

class VisitExamination extends SchemasVisitExamination implements ModulePatientVisitExamination
{
    public function prepareVisitExaminationSignOff(Model &$visit_examination_model, VisitExaminationData &$visit_examination_dto): Model
    {
        $visit_examination = parent::prepareVisitExaminationSignOff($visit_examination_model, $visit_examination_dto);
        $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $visit_examination->visitRegistration;
        $patient_model = $visit_examination_dto->patient_model ?? $visit_examination_model->patient ?? null;
        if ($this->is_recently_created){
            $this->updateDashboardStatistics($visit_examination,'unsigned');
        }

        if ($this->is_sign_off){
            if (isset($patient_model)) {
                $this->prepareStoreObservationSatuSehatLog($visit_examination_model,$visit_registration_model,$patient_model);
                // $payload = $this->prepareSatuSehatObservationPayload($visit_examination, $visit_registration_model, $patient_model);
                // $this->dispatchSatuSehatSync($visit_examination, $patient_model, $payload);
            }
            $treatments = $visit_examination->treatments;
            if (count($treatments) > 0){
                $this->updateDashboardStatistics($visit_examination,'treatment',[
                    'treatments' => $treatments
                ]);
            }

            $diagnoses = $visit_examination->diagnoses;
            if (count($treatments) > 0){
                $this->updateDashboardStatistics($visit_examination,'diagnose',[
                    'diagnoses' => $diagnoses
                ]);
            }
            $this->updateDashboardStatistics($visit_examination,'unsigned-decrement');
        }

        return $visit_examination;
    }

    public function prepareStoreObservationSatuSehatLog(Model $visit_examination_model, Model $visit_registration_model, Model $patient_model, ?array $payload = null, ? array $existing = null){
        $payload = $this->prepareSatuSehatObservationPayload($visit_examination_model, $visit_registration_model, $patient_model);
        $this->dispatchSatuSehatSync($visit_examination_model, $patient_model, $payload, $existing);
    }

    /**
     * Update dashboard statistics when a new patient is created.
     * Updates both total patients count and new patients count.
     *
     * @param Model $patient
     * @return void
     */
    private function updateDashboardStatistics(Model $visit_examination,string $type,?array $data = []): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(DashboardMetricsService::class);
            switch ($type) {
                case 'treatment'         : 
                    $treatments = $data['treatments'];
                    $dashboardService->incrementNewTreatment(count($treatments));
                    $debt = 0;
                    $patient_model = $visit_examination->patient;
                    $visit_registration_model = $visit_examination->visitRegistration;
                    foreach ($treatments as $treatment) {
                        $exam = $treatment->exam;
                        $debt += $exam['treatment']['price'];
                        $practitioner_evaluations = $treatment['prop_practitioner_evaluations'] ?? [];
                        if (count($practitioner_evaluations) > 0){
                            $practitioner_evaluation = end($practitioner_evaluations);
                            $practitioner_name = $practitioner_evaluation['name'];
                        }
                        $dashboardService->incrementNewTreatmentDiagnose([
                            'patient'     => $patient_model->name,
                            'code'        => $exam['treatment']['reference']['treatment_code'],
                            'type'        => 'Tindakan',
                            'description' => $exam['name'],
                            'poli'        => $visit_registration_model->prop_medic_service['name'] ?? $visit_registration_model->medicService->name,
                            'date'        => $treatment->created_at->format('Y-m-d'),
                            'doctor'      => $practitioner_name ?? null
                        ]);
                        
                    }
                    $dashboardService->incrementNewUnpaid($debt);
                    if ($debt > 0){
                        $dashboardService->incrementNewPending();
                    }
                break;
                case 'diagnose'         : 
                    $diagnoses = $data['diagnoses'];
                    $patient_model = $visit_examination->patient;
                    $visit_registration_model = $visit_examination->visitRegistration;
                    foreach ($diagnoses as $diagnose) {
                        $exam = $diagnose->exam;
                        $practitioner_evaluations = $diagnose['prop_practitioner_evaluations'] ?? [];
                        if (count($practitioner_evaluations) > 0){
                            $practitioner_evaluation = end($practitioner_evaluations);
                            $practitioner_name = $practitioner_evaluation['name'];
                        }
                        $dashboardService->incrementNewTreatmentDiagnose([
                            'patient'     => $patient_model->name,
                            'code'        => $exam['code'],
                            'type'        => 'Diagnosa',
                            'description' => $exam['name'],
                            'poli'        => $visit_registration_model->prop_medic_service['name'] ?? $visit_registration_model->medicService->name,
                            'date'        => $diagnose->created_at->format('Y-m-d'),
                            'doctor'      => $practitioner_name ?? null
                        ]);
                        
                    }
                break;
                case 'unsigned'          : $dashboardService->incrementNewUnsignedVisit();break;
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
        
        if (isset($visit_registration_model->practitionerEvaluation)){
            $practitioner = $visit_registration_model->practitionerEvaluation->practitioner;
        }

        $payload = [
            "encounter_code" => $visit_registration_model->ihs_number,
            "encounter_display" => "Pemeriksaan Pasien " . $patient_model->name,
            "status" => "final",
            "patient_code" => $patient_model->prop_card_identity['ihs_number'] ?? null,
            "organization_code" => config('satu-sehat.organization_id'),
            // "practitioner_codes" => [
            //     "10006926841"
            // ],
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
        if (isset($practitioner)){
            $payload['practitioner_codes'] = [
                $practitioner?->prop_card_identity['ihs_number'] ?? null
            ];
        }
        return $payload;
    }

    /**
     * Dispatch observation data to Satu Sehat via async job if enabled.
     */
    private function dispatchSatuSehatSync(Model $visit_examination_model, Model $patient_model, array $payload,? array $existing = null): void
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
                $payload,
                $existing['id'] ?? null,
                $existing['referenceId'] ?? null,
                $existing['referenceType'] ?? null
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
