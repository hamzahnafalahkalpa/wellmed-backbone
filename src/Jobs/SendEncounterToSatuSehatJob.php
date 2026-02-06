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

class SendEncounterToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData, HasSatuSehatIntegration;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected string $visitRegistrationId;
    protected string $patientId;
    protected array $formPayload;

    public function __construct(mixed $tenantId, string $visitRegistrationId, string $patientId, array $formPayload)
    {
        $this->tenantId = $tenantId;
        $this->visitRegistrationId = $visitRegistrationId;
        $this->patientId = $patientId;
        $this->formPayload = $formPayload;
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);

            // Get visit registration model
            $visitRegistrationModel = app(config('database.models.VisitRegistration'))->find($this->visitRegistrationId);

            if (!$visitRegistrationModel) {
                Log::channel('satu-sehat')->warning("Visit registration not found: {$this->visitRegistrationId}");
                return;
            }

            // Get patient model
            $patientModel = app(config('database.models.Patient'))->find($this->patientId);

            if (!$patientModel) {
                Log::channel('satu-sehat')->warning("Patient not found: {$this->patientId}");
                return;
            }

            // Send encounter to Satu Sehat
            $encounter_satu_sehat = app(config('app.contracts.EncounterSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareStoreEncounterSatuSehat(
                    $this->requestDTO(config('app.contracts.EncounterSatuSehatData'), [
                        'model' => $visitRegistrationModel,
                        'form'  => $this->formPayload
                    ])
                );

            $ihsNumber = $encounter_satu_sehat->response['id'] ?? null;

            // Update visit registration with IHS number
            $visitRegistrationModel->ihs_number = $ihsNumber;
            $visitRegistrationModel->save();

            // Update patient integration sync tracking (also adds log entry)
            $this->updatePatientSyncCounter($patientModel, 'encounter');

            // Update workspace integration tracking
            $workspaceModel = $this->getWorkspaceModel();
            if ($workspaceModel) {
                $this->updateWorkspaceSyncCounter($workspaceModel, 'encounter');
            }

            // Update Elasticsearch dashboard metrics
            $this->updateDashboardWorkspaceIntegration('encounter');
            $this->updateDashboardPatientIntegration((string) $this->patientId, 'encounter');

            Log::channel('satu-sehat')->info("Encounter sent to Satu Sehat successfully", [
                'visit_registration_id' => $this->visitRegistrationId,
                'patient_id' => $this->patientId,
                'ihs_number' => $ihsNumber
            ]);

        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to send encounter to Satu Sehat", [
                'visit_registration_id' => $this->visitRegistrationId,
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
        Log::channel('satu-sehat')->critical("SendEncounterToSatuSehatJob failed after all retries", [
            'visit_registration_id' => $this->visitRegistrationId,
            'patient_id' => $this->patientId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
