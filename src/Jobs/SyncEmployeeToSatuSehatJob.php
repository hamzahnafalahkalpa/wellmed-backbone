<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncEmployeeToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData;

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
            // Get patient model
            $employeeModel = app(config('database.models.Employee'))->find($this->employeeId);
            if (!$employeeModel) {
                Log::channel('satu-sehat')->warning("Employee not found: {$this->employeeId}");
                return;
            }
            $people = $employeeModel->people;
            if (isset($people) && isset($people->prop_card_identity['nik'])){
                $nik = $people->prop_card_identity['nik'];
            }else{
                Log::channel('satu-sehat')->warning("Nik required for practitioner");
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
            // Update employee with IHS number
            $prop_card_identity = $employeeModel->prop_card_identity ?? [];
            $prop_card_identity['ihs_number'] = $practitioner_satu_sehat[0]['resource']['id'] ?? null;
            $employeeModel->setAttribute('prop_card_identity', $prop_card_identity);
            $employeeModel->save();
            Log::channel('satu-sehat')->info("Employee sent to Satu Sehat successfully", [
                'patient_id' => $this->employeeId,
                'ihs_number' => $prop_card_identity['ihs_number'] ?? null
            ]);

        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to send patient to Satu Sehat", [
                'patient_id' => $this->employeeId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $th;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('satu-sehat')->critical("SendEmployeeToSatuSehatJob failed after all retries", [
            'patient_id' => $this->employeeId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
