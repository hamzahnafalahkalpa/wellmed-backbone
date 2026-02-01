<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPatientToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, HasRequestData;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected mixed $tenantId;
    protected mixed $patientId;
    protected array $formPayload;

    public function __construct(mixed $tenantId, mixed $patientId, array $formPayload)
    {
        $this->tenantId = $tenantId;
        $this->patientId = $patientId;
        $this->formPayload = $formPayload;   
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);
            // Get patient model
            $patientModel = app(config('database.models.Patient'))->find($this->patientId);
            if (!$patientModel) {
                Log::channel('satu-sehat')->warning("Patient not found: {$this->patientId}");
                return;
            }

            $dto = $this->requestDTO(config('app.contracts.PatientSatuSehatData'), [
                'model' => $patientModel,
                'form'  => $this->formPayload
            ]);
            Log::channel('satu-sehat')->info("DTO", [
                'dto' => $dto->toArray()
            ]);
            // Send to Satu Sehat
            $patient_satu_sehat = app(config('app.contracts.PatientSatuSehat'))
                ->useAccessToSatuSehat()
                ->prepareStorePatientSatuSehat(
                    $this->requestDTO(config('app.contracts.PatientSatuSehatData'), [
                        'model' => $patientModel,
                        'form'  => $this->formPayload
                    ])
                );
            // Update patient with IHS number
            $prop_card_identity = $patientModel->prop_card_identity ?? [];
            $prop_card_identity['ihs_number'] = $patient_satu_sehat->response['id'] ?? null;
            $patientModel->setAttribute('prop_card_identity', $prop_card_identity);

            // Initialize integration structure if not exists
            $integration = $patientModel->integration ?? [];
            if (!isset($integration['satu_sehat'])) {
                $integration['satu_sehat'] = [
                    'progress' => 0,
                    'to' => 0,
                    'from' => 0,
                    'syncs' => [
                        ['label' => 'Kunjungan', 'flag' => 'encounter', 'progress' => 0, 'to' => 0, 'from' => 0],
                        ['label' => 'Resep', 'flag' => 'dispense', 'progress' => 0, 'to' => 0, 'from' => 0],
                        ['label' => 'Diagnosa', 'flag' => 'condition', 'progress' => 0, 'to' => 0, 'from' => 0]
                    ]
                ];
                $patientModel->setAttribute('integration', $integration);
            }

            $patientModel->save();
            Log::channel('satu-sehat')->info("Patient sent to Satu Sehat successfully", [
                'patient_id' => $this->patientId,
                'ihs_number' => $patient_satu_sehat->response['id'] ?? null
            ]);

        } catch (\Throwable $th) {
            Log::channel('satu-sehat')->error("Failed to send patient to Satu Sehat", [
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
        Log::channel('satu-sehat')->critical("SendPatientToSatuSehatJob failed after all retries", [
            'patient_id' => $this->patientId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);
    }
}
