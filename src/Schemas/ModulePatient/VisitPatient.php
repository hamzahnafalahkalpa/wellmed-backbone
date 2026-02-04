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
        return $this;
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
