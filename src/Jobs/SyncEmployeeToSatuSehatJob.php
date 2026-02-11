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

class SyncEmployeeToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData, HasSatuSehatIntegration;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected mixed $employeeId;

    public function __construct(mixed $tenantId, mixed $employeeId)
    {
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);

            // Get employee model
            $employeeModel = app(config('database.models.Employee'))->find($this->employeeId);
            if (!$employeeModel) {
                Log::channel('satu-sehat')->warning("Employee not found: {$this->employeeId}");
                return;
            }

            $people = $employeeModel->people;
            if (isset($people) && isset($people->prop_card_identity['nik'])) {
                $nik = $people->prop_card_identity['nik'];
            } else {
                Log::channel('satu-sehat')->warning("NIK required for practitioner");
                return;
            }

            $dto = $this->requestDTO(config('app.contracts.PractitionerSatuSehatData'), [
                'model' => $employeeModel,
                'params' => [
                    'nik' => $nik
                ]
            ]);

            Log::channel('satu-sehat')->info("DTO", [
                'dto' => $dto->toArray()
            ]);

            // Send to Satu Sehat
            $practitioner_satu_sehat = app(config('app.contracts.PractitionerSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareViewPractitionerSatuSehatList($dto);

            $ihsNumber = $practitioner_satu_sehat[0]['resource']['id'] ?? null;

            // Update employee with IHS number
            $propCardIdentity = $employeeModel->prop_card_identity ?? [];
            $propCardIdentity['ihs_number'] = $ihsNumber;
            $employeeModel->setAttribute('prop_card_identity', $propCardIdentity);
            $employeeModel->save();

            // Update workspace integration tracking
            $workspaceModel = $this->getWorkspaceModel();
            if ($workspaceModel) {
                $this->updateWorkspaceSyncCounter($workspaceModel, 'practitioner');
            }

            // Update Elasticsearch dashboard metrics
            $this->updateDashboardWorkspaceIntegration('practitioner');

            // Update Satu Sehat dashboard (increment satuSehatCount)
            $this->updateSatuSehatDashboard('practitioners');

            Log::channel('satu-sehat')->info("Employee synced to Satu Sehat successfully", [
                'employee_id' => $this->employeeId,
                'ihs_number' => $ihsNumber
            ]);

        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to sync employee to Satu Sehat", [
                'employee_id' => $this->employeeId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $th;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('satu-sehat')->critical("SyncEmployeeToSatuSehatJob failed after all retries", [
            'employee_id' => $this->employeeId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
