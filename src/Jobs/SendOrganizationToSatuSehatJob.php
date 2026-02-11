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

class SendOrganizationToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData, HasSatuSehatIntegration;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected mixed $ogranizationId;
    protected array $formPayload;

    public function __construct(mixed $tenantId, mixed $ogranizationId, array $formPayload)
    {
        $this->tenantId = $tenantId;
        $this->ogranizationId = $ogranizationId;
        $this->formPayload = $formPayload;
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);

            // Get workspace/organization model
            $organizationModel = app(config('database.models.Workspace'))->find($this->ogranizationId);
            if (!$organizationModel) {
                Log::channel('satu-sehat')->warning("Organization not found: {$this->ogranizationId}");
                return;
            }

            $dto = $this->requestDTO(config('app.contracts.OrganizationSatuSehatData'), [
                'model' => $organizationModel,
                'form'  => $this->formPayload
            ]);

            Log::channel('satu-sehat')->info("DTO", [
                'dto' => $dto->toArray()
            ]);

            // Send to Satu Sehat
            $organization_satu_sehat = app(config('app.contracts.OrganizationSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareStoreOrganizationSatuSehat($dto);

            $ihsNumber = $organization_satu_sehat->response[0]['resource']['id'] ?? null;

            // Update workspace with IHS number using new integration structure
            if ($ihsNumber) {
                $this->updateWorkspaceIhsNumber($organizationModel, $ihsNumber);
                config(['satu-sehat.client_organization_id' => $ihsNumber]);
            }

            // Update Elasticsearch dashboard metrics
            $this->updateDashboardWorkspaceIntegration('organization');

            // Update Satu Sehat dashboard (increment satuSehatCount)
            $this->updateSatuSehatDashboard('organization');

            Log::channel('satu-sehat')->info("Organization sent to Satu Sehat successfully", [
                'organization_id' => $this->ogranizationId,
                'ihs_number' => $ihsNumber
            ]);

        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to send organization to Satu Sehat", [
                'organization_id' => $this->ogranizationId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $th;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('satu-sehat')->critical("SendOrganizationToSatuSehatJob failed after all retries", [
            'organization_id' => $this->ogranizationId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
