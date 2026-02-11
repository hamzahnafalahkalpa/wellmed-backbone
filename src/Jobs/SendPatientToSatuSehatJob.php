<?php

namespace Projects\WellmedBackbone\Jobs;

use Exception;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Jobs\Concerns\HasSatuSehatIntegration;
use Projects\WellmedBackbone\Services\DashboardMetricsService;

class SendPatientToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData, HasSatuSehatIntegration;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected mixed $id;
    protected mixed $referenceId;
    protected ?string $referenceType = null;
    protected mixed $patientId;
    protected array $formPayload;
    protected ?object $dto;

    public function __construct(mixed $tenantId, mixed $patientId, array $formPayload, mixed $id = null, mixed $referenceId = null, ?string $referenceType = null)
    {
        $this->tenantId = $tenantId;
        $this->patientId = $patientId;
        $this->id = $id;
        $this->referenceId = $referenceId;
        $this->referenceType = $referenceType;
        $this->formPayload = $formPayload;
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);
            // tenancy()->initialize($this->tenantId);

            // Get patient model
            $patientModel = app(config('database.models.Patient'))->find($this->patientId);

            if (!$patientModel) {
                Log::channel('satu-sehat')->warning("Patient not found: {$this->patientId}");
                throw new Exception('Patient not found: '.$this->patientId);
            }

            $this->dto = $dto = $this->requestDTO(config('app.contracts.PatientSatuSehatData'), [
                'id' => $this->id ?? null,
                'reference_type' => $this->referenceType ?? null,
                'reference_id' => $this->referenceId ?? null,
                'model' => $patientModel,
                'form'  => $this->formPayload
            ]);

            Log::channel('satu-sehat')->info("DTO", [
                'dto' => $dto->toArray()
            ]);

            // Send to Satu Sehat
            $patient_satu_sehat = app(config('app.contracts.PatientSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareStorePatientSatuSehat($dto);

            $ihsNumber = $patient_satu_sehat->response['id'] ?? null;

            // Update patient with IHS number using new integration structure
            if ($ihsNumber) {
                $this->updatePatientIhsNumber($patientModel, $ihsNumber);
            }

            // Update workspace sync counter for patient
            $workspaceModel = $this->getWorkspaceModel();
            if ($workspaceModel) {
                $this->updateWorkspaceSyncCounter($workspaceModel, 'patient');
            }

            // Update Elasticsearch dashboard metrics
            $this->updateDashboardWorkspaceIntegration('patient');
            $this->updateDashboardPatientIntegration((string) $this->patientId, 'patient');

            // Update Satu Sehat dashboard (increment satuSehatCount)
            $this->updateSatuSehatDashboard('patients');

            Log::channel('satu-sehat')->info("Patient sent to Satu Sehat successfully", [
                'patient_id' => $this->patientId,
                'ihs_number' => $ihsNumber
            ]);

        } catch (\Throwable $th) {            
            Log::channel('satu-sehat')->error("Failed to send patient to Satu Sehat", [
                'patient_id' => $this->patientId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            $this->updateDashboardStatistics($this->patientId, 'unsynced-patients');

            // Re-throw to trigger retry mechanism
            throw $th;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('satu-sehat')->critical("SendPatientToSatuSehatJob failed after all retries", [
            'patient_id' => $this->patientId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }

    /**
     * Update dashboard statistics when sync fails.
     *
     * @param mixed $patientId
     * @param string $type
     * @return void
     */
    private function updateDashboardStatistics(mixed $patientId, string $type): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(DashboardMetricsService::class);

            switch ($type) {
                case 'unsynced-patients':
                    $dashboardService->incrementNewUnsycedVisit();
                    break;
            }

            Log::channel('elasticsearch')->info('Dashboard statistics updated for patient sync failure', [
                'patient_id' => $patientId
            ]);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update dashboard statistics', [
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
