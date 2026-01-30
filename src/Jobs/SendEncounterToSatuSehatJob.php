<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEncounterToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected int $tenantId;
    protected int $visitRegistrationId;
    protected int $patientId;
    protected array $formPayload;

    public function __construct(int $tenantId, int $visitRegistrationId, int $patientId, array $formPayload)
    {
        $this->tenantId = $tenantId;
        $this->visitRegistrationId = $visitRegistrationId;
        $this->patientId = $patientId;
        $this->formPayload = $formPayload;
        $this->onQueue('satusehat');
    }

    public function handle(): void
    {
        try {
            // Set tenant context for multi-tenant isolation
            MicroTenant::tenantImpersonate($this->tenantId);

            // Get visit registration model
            $visitRegistrationModel = app(config('app.models.VisitRegistration'))->find($this->visitRegistrationId);

            if (!$visitRegistrationModel) {
                Log::channel('satu-sehat')->warning("Visit registration not found: {$this->visitRegistrationId}");
                return;
            }

            // Get patient model
            $patientModel = app(config('app.models.Patient'))->find($this->patientId);

            if (!$patientModel) {
                Log::channel('satu-sehat')->warning("Patient not found: {$this->patientId}");
                return;
            }

            // Send encounter to Satu Sehat
            $encounter_satu_sehat = app(config('app.schemas.encounter_satu_sehat'))
                ->useAccessToSatuSehat()
                ->prepareStoreEncounterSatuSehat(
                    app()->make(config('app.contracts.EncounterSatuSehatData'), [
                        'model' => $visitRegistrationModel,
                        'form'  => $this->formPayload
                    ])
                );

            // Update visit registration with IHS number
            $visitRegistrationModel->ihs_number = $encounter_satu_sehat->response['id'] ?? null;
            $visitRegistrationModel->save();

            // Update patient integration sync tracking
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
            }

            $satu_sehat = &$integration['satu_sehat'];

            // Update overall counter
            $satu_sehat['to'] = ($satu_sehat['to'] ?? 0) + 1;
            $satu_sehat['from'] = ($satu_sehat['from'] ?? 0) + 1;

            // Update encounter-specific counter
            $syncs = &$satu_sehat['syncs'];
            foreach ($syncs as &$sync) {
                if ($sync['flag'] == 'encounter') {
                    $sync['to'] = ($sync['to'] ?? 0) + 1;
                    $sync['from'] = ($sync['from'] ?? 0) + 1;
                    $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] * 100) / $sync['to'], 2) : 0;
                    break;
                }
            }

            // Calculate overall progress
            $totalSyncs = count($satu_sehat['syncs']);
            $completedProgress = 0;
            foreach ($satu_sehat['syncs'] as $sync) {
                $completedProgress += ($sync['progress'] ?? 0);
            }
            $satu_sehat['progress'] = $totalSyncs > 0 ? round($completedProgress / $totalSyncs, 2) : 0;

            $patientModel->setAttribute('integration', $integration);
            $patientModel->save();

            // Update workspace integration tracking
            // $this->updateWorkspaceIntegration();

            Log::channel('satu-sehat')->info("Encounter sent to Satu Sehat successfully", [
                'visit_registration_id' => $this->visitRegistrationId,
                'patient_id' => $this->patientId,
                'ihs_number' => $encounter_satu_sehat->response['id'] ?? null
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

    protected function updateWorkspaceIntegration(): void
    {
        $workspace = config('app.workspace_model');

        if (!$workspace) {
            Log::channel('satu-sehat')->warning("Workspace model not found in config");
            return;
        }

        $integration = $workspace->integration ?? [];

        if (!isset($integration['satu_sehat'])) {
            $integration['satu_sehat'] = [
                'progress' => 0,
                'general' => [
                    'ihs_number' => null
                ],
                'syncs' => [
                    ['flag' => 'encounter', 'label' => 'Kunjungan', 'progress' => 0, 'to' => 0, 'from' => 0],
                    ['flag' => 'condition', 'label' => 'Diagnosa', 'progress' => 0, 'to' => 0, 'from' => 0],
                    ['flag' => 'dispense', 'label' => 'Resep', 'progress' => 0, 'to' => 0, 'from' => 0]
                ]
            ];
        }

        $satu_sehat = &$integration['satu_sehat'];
        $syncs = &$satu_sehat['syncs'];

        // Update encounter-specific counter in workspace
        foreach ($syncs as &$sync) {
            if ($sync['flag'] == 'encounter') {
                $sync['to'] = ($sync['to'] ?? 0) + 1;
                $sync['from'] = ($sync['from'] ?? 0) + 1;
                $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] * 100) / $sync['to'], 2) : 0;
                break;
            }
        }

        // Calculate overall workspace progress
        $totalSyncs = count($satu_sehat['syncs']);
        $completedProgress = 0;
        foreach ($satu_sehat['syncs'] as $sync) {
            $completedProgress += ($sync['progress'] ?? 0);
        }
        $satu_sehat['progress'] = $totalSyncs > 0 ? round($completedProgress / $totalSyncs, 2) : 0;

        $workspace->integration = $integration;
        $workspace->save();

        Log::channel('satu-sehat')->info("Workspace integration updated for encounter", [
            'workspace_id' => $workspace->getKey(),
            'progress' => $satu_sehat['progress']
        ]);
    }
}
