<?php

namespace Projects\WellmedBackbone\Jobs;

use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendObservationToSatuSehatJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

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

            // Get visit examination model
            $visitExaminationModel = app(config('app.models.VisitExamination'))->find($this->visitExaminationId);

            if (!$visitExaminationModel) {
                Log::channel('satu-sehat')->warning("Visit examination not found: {$this->visitExaminationId}");
                return;
            }

            // Get patient model
            $patientModel = app(config('app.models.Patient'))->find($this->patientId);

            if (!$patientModel) {
                Log::channel('satu-sehat')->warning("Patient not found: {$this->patientId}");
                return;
            }

            // Send observation to Satu Sehat
            $observation_satu_sehat = app(config('app.schemas.observation_satu_sehat'))
                ->useAccessToSatuSehat()
                ->prepareStoreObservationSatuSehat(
                    app()->make(config('app.contracts.ObservationSatuSehatData'), [
                        'model' => $visitExaminationModel,
                        'form'  => $this->formPayload
                    ])
                );

            // Optionally update visit examination with IHS number if needed
            // $visitExaminationModel->ihs_number = $observation_satu_sehat->response['id'] ?? null;
            // $visitExaminationModel->save();

            // Update patient integration tracking
            $this->updatePatientIntegration($patientModel);

            // Update workspace integration tracking
            $this->updateWorkspaceIntegration();

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

    protected function updatePatientIntegration($patientModel): void
    {
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

        // Calculate overall progress
        $totalSyncs = count($satu_sehat['syncs']);
        $completedProgress = 0;
        foreach ($satu_sehat['syncs'] as $sync) {
            $completedProgress += ($sync['progress'] ?? 0);
        }
        $satu_sehat['progress'] = $totalSyncs > 0 ? round($completedProgress / $totalSyncs, 2) : 0;

        $patientModel->setAttribute('integration', $integration);
        $patientModel->save();
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

        // Update overall observation counter (not a specific sync flag, but counts toward overall activity)
        // For workspace, we track organization-wide metrics
        $syncs = &$satu_sehat['syncs'];

        // Increment overall sync activity
        foreach ($syncs as &$sync) {
            // Increment all sync types as observation is a general health data sync
            $sync['to'] = ($sync['to'] ?? 0) + 1;
            $sync['from'] = ($sync['from'] ?? 0) + 1;
            $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] * 100) / $sync['to'], 2) : 0;
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

        Log::channel('satu-sehat')->info("Workspace integration updated", [
            'workspace_id' => $workspace->getKey(),
            'progress' => $satu_sehat['progress']
        ]);
    }
}
