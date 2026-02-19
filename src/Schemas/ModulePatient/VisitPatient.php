<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData;
use Hanafalah\ModulePatient\Schemas\VisitPatient as SchemasVisitPatient;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\VisitPatient as ModulePatientVisitPatient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Services\DashboardMetricsService;

class VisitPatient extends SchemasVisitPatient implements ModulePatientVisitPatient
{
    protected function afterVisitPatientCreated(Model &$visit_patient_model, VisitPatientData &$visit_patient_dto): self{
        parent::afterVisitPatientCreated($visit_patient_model, $visit_patient_dto);
        if ($this->is_recently_created){
            $this->updateDashboardStatistics($visit_patient_model,'total-transaction');
            $this->updateDashboardStatistics($visit_patient_model,'motivational-stats');
        }
        // Send notifications to practitioners
        if (isset($visit_patient_dto->practitioner_evaluations)) {
            $this->dispatchPractitionerNotifications($visit_patient_model, $visit_patient_dto);
        }
        return $this;
    }

    protected function dispatchPractitionerNotifications(Model $visitPatient, VisitPatientData $visitPatientDto): void
    {
        $patient = $visitPatientDto->patient_model ?? $visitPatient->patient;
        $patientName = $patient?->name ?? 'Patient';

        foreach ($visitPatientDto->practitioner_evaluations as $practitionerEvaluationDto) {
            $practitionerId = $practitionerEvaluationDto->practitioner_id ?? null;
            $practitionerType = $practitionerEvaluationDto->practitioner_type ?? config('module-patient.practitioner', 'User');

            if (!$practitionerId) {
                continue;
            }

            // Get the user to notify
            $user = $this->resolvePractitionerUser($practitionerId, $practitionerType);
            if (!$user) {
                continue;
            }

            // Dispatch notification asynchronously after commit
            dispatch(function () use ($user, $visitPatient, $patientName) {
                try {
                    app(\Hanafalah\ModuleNotification\Services\NotificationService::class)
                        ->notifyUser($user, [
                            'icon' => 'visit',
                            'title' => 'New Visit Assigned',
                            'content' => "You have a new visit with patient {$patientName}",
                            'type' => 'visit_created',
                            'data' => [
                                'visit_patient_id' => $visitPatient->id,
                                'patient_id' => $visitPatient->patient_id,
                            ],
                        ]);
                } catch (\Throwable $e) {
                    report($e);
                }
            })->afterCommit();
        }
    }

    protected function resolvePractitionerUser($practitionerId, string $practitionerType): ?Model
    {
        $practitionerModelClass = config('database.models.' . $practitionerType);
        if (!$practitionerModelClass || !class_exists($practitionerModelClass)) {
            return null;
        }

        $practitioner = app($practitionerModelClass)->find($practitionerId);
        if (!$practitioner) {
            return null;
        }

        // If practitioner is already a User, return it directly
        if ($practitionerType === 'User') {
            return $practitioner;
        }

        // If practitioner has a user relationship, use it
        if (method_exists($practitioner, 'user')) {
            return $practitioner->user;
        }

        // If practitioner has an employee relationship with user
        if (method_exists($practitioner, 'employee')) {
            $employee = $practitioner->employee;
            if ($employee && method_exists($employee, 'user')) {
                return $employee->user;
            }
        }

        return null;
    }

    /**
     * Update dashboard statistics when a new patient is created.
     * Updates both total patients count and new patients count.
     *
     * @param Model $patient
     * @return void
     */
    private function updateDashboardStatistics(Model $visit_patient_model,string $type,?array $data = []): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(DashboardMetricsService::class);
            switch ($type) {
                case 'motivational-stats': 
                    $dashboardService->incrementMotivational();
                break;
                case 'total-transaction': 
                    $dashboardService->incrementNewTransaction();
                break;
            }

            Log::channel('elasticsearch')->info('Dashboard statistics updated for new visit_patient', [
                'visit_patient_id' => $visit_patient_model->getKey()
            ]);

        } catch (\Throwable $e) {
            // Don't fail visit_patient_model creation if dashboard update fails
            Log::channel('elasticsearch')->error('Failed to update dashboard statistics', [
                'visit_patient_id' => $visit_patient_model->getKey(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
