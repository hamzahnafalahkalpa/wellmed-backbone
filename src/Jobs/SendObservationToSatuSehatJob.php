<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Jobs\Concerns\HasSatuSehatIntegration;

class SendObservationToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasSatuSehatIntegration, HasRequestData;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected mixed $visitExaminationId;
    protected mixed $patientId;
    protected array $formPayload;

    public function __construct(mixed $tenantId, mixed $visitExaminationId, mixed $patientId, array $formPayload)
    {
        $this->tenantId = $tenantId;
        $this->visitExaminationId = $visitExaminationId;
        $this->patientId = $patientId;
        $this->formPayload = $formPayload;
        $this->onQueue('satusehat');
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);
            tenancy()->initialize($this->tenantId);

            // Get visit examination model
            $visitExaminationModel = app(config('database.models.VisitExamination'))->find($this->visitExaminationId);
            if (!$visitExaminationModel) {
                Log::channel('satu-sehat')->warning("Visit examination not found: {$this->visitExaminationId}");
                return;
            }

            // Get patient model
            $patientModel = app(config('database.models.Patient'))->find($this->patientId);

            if (!$patientModel) {
                Log::channel('satu-sehat')->warning("Patient not found: {$this->patientId}");
                return;
            }

            // Send observation to Satu Sehat
            $observation_satu_sehat = app(config('app.contracts.ObservationSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareStoreObservationSatuSehat(
                    $this->requestDTO(config('app.contracts.ObservationSatuSehatData'), [
                        'model' => $visitExaminationModel,
                        'form'  => $this->formPayload
                    ])
                );

            // Update patient integration tracking - observation goes to logs
            $this->updatePatientLog($patientModel, 'observation', 'Observasi Pasien');

            // Update workspace integration tracking
            $workspaceModel = $this->getWorkspaceModel();
            if ($workspaceModel) {
                $this->updateWorkspaceSyncCounter($workspaceModel, 'encounter');
            }

            // Update Elasticsearch dashboard metrics
            $this->updateDashboardWorkspaceIntegration('encounter');
            $this->updateDashboardPatientIntegration((string) $this->patientId, 'observation');

            Log::channel('satu-sehat')->info("Observation sent to Satu Sehat successfully", [
                'visit_examination_id' => $this->visitExaminationId,
                'patient_id' => $this->patientId,
                'response' => $observation_satu_sehat->response ?? []
            ]);
        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to send observation to Satu Sehat", [
                'visit_examination_id' => $this->visitExaminationId,
                'patient_id' => $this->patientId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $th;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('satu-sehat')->critical("SendObservationToSatuSehatJob failed after all retries", [
            'visit_examination_id' => $this->visitExaminationId,
            'patient_id' => $this->patientId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
