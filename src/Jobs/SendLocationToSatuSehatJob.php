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

class SendLocationToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData, HasSatuSehatIntegration;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected mixed $roomId;
    protected array $formPayload;

    public function __construct(mixed $tenantId, mixed $roomId, array $formPayload)
    {
        $this->tenantId = $tenantId;
        $this->roomId = $roomId;
        $this->formPayload = $formPayload;
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);

            // Get room model
            $roomModel = app(config('database.models.Room'))->find($this->roomId);
            if (!$roomModel) {
                Log::channel('satu-sehat')->warning("Location not found: {$this->roomId}");
                return;
            }

            $dto = $this->requestDTO(config('app.contracts.LocationSatuSehatData'), [
                'model' => $roomModel,
                'form'  => $this->formPayload
            ]);

            Log::channel('satu-sehat')->info("DTO", [
                'dto' => $dto->toArray()
            ]);
            // Send to Satu Sehat
            $location_satu_sehat = app(config('app.contracts.LocationSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareStoreLocationSatuSehat($dto);

            $ihsNumber = $location_satu_sehat->response['id'] ?? null;

            // Update room with IHS number
            $roomModel->ihs_number = $ihsNumber;
            $roomModel->save();

            // Update workspace integration tracking
            $workspaceModel = $this->getWorkspaceModel();
            if ($workspaceModel) {
                $this->updateWorkspaceSyncCounter($workspaceModel, 'location');
            }

            // Update Elasticsearch dashboard metrics
            $this->updateDashboardWorkspaceIntegration('location');

            Log::channel('satu-sehat')->info("Location sent to Satu Sehat successfully", [
                'room_id' => $this->roomId,
                'ihs_number' => $ihsNumber
            ]);

        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to send location to Satu Sehat", [
                'room_id' => $this->roomId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $th;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('satu-sehat')->critical("SendLocationToSatuSehatJob failed after all retries", [
            'room_id' => $this->roomId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
