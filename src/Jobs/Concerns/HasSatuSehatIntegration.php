<?php

namespace Projects\WellmedBackbone\Jobs\Concerns;

use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Services\DashboardMetricsService;
use Projects\WellmedBackbone\Services\PatientDashboardMetricsService;

/**
 * Trait HasSatuSehatIntegration
 *
 * Handles Satu Sehat integration tracking for Jobs.
 * Updates both the model's integration field and Elasticsearch dashboard metrics.
 */
trait HasSatuSehatIntegration
{
    /**
     * Get default patient integration payload structure.
     *
     * @return array
     */
    protected function getDefaultPatientIntegrationPayload(): array
    {
        return [
            'flag' => 'satu-sehat',
            'label' => 'Satu Sehat',
            'progress' => 0,
            'pending' => 0,
            'syncs' => [
                [
                    'flag' => 'encounter',
                    'label' => 'Kunjungan',
                    'from' => null,
                    'to' => null,
                    'progress' => 0,
                    'last_updated_at' => null
                ],
                [
                    'flag' => 'observation',
                    'label' => 'Observasi',
                    'from' => null,
                    'to' => null,
                    'progress' => 0,
                    'last_updated_at' => null
                ]
            ],
            'logs' => []
        ];
    }

    /**
     * Get default workspace integration payload structure.
     *
     * @return array
     */
    protected function getDefaultWorkspaceIntegrationPayload(): array
    {
        return [
            'progress' => 0,
            'general' => [
                'ihs_number' => null
            ],
            'syncs' => [
                [
                    'flag' => 'patient',
                    'label' => 'Pasien',
                    'from' => null,
                    'to' => null,
                    'progress' => 0,
                    'last_updated_at' => null
                ],
                [
                    'flag' => 'encounter',
                    'label' => 'Kunjungan',
                    'from' => null,
                    'to' => null,
                    'progress' => 0,
                    'last_updated_at' => null
                ],
                [
                    'flag' => 'observation',
                    'label' => 'Observasi',
                    'from' => null,
                    'to' => null,
                    'progress' => 0,
                    'last_updated_at' => null
                ]
            ],
            'logs' => []
        ];
    }

    /**
     * Initialize patient integration if not exists.
     *
     * @param mixed $patientModel
     * @return array
     */
    protected function initializePatientIntegration($patientModel): array
    {
        $integration = $patientModel->integration ?? [];

        if (!isset($integration['satu_sehat'])) {
            $integration['satu_sehat'] = $this->getDefaultPatientIntegrationPayload();
        }

        return $integration;
    }

    /**
     * Initialize workspace integration if not exists.
     *
     * @param mixed $workspaceModel
     * @return array
     */
    protected function initializeWorkspaceIntegration($workspaceModel): array
    {
        $integration = $workspaceModel->integration ?? [];

        if (!isset($integration['satu_sehat'])) {
            $integration['satu_sehat'] = $this->getDefaultWorkspaceIntegrationPayload();
        }

        return $integration;
    }

    /**
     * Update patient IHS number and save.
     *
     * @param mixed $patientModel
     * @param string $ihsNumber
     * @return void
     */
    protected function updatePatientIhsNumber($patientModel, string $ihsNumber): void
    {
        $integration = $this->initializePatientIntegration($patientModel);

        $patientModel->setAttribute('integration', $integration);

        // Also update prop_card_identity for backward compatibility
        $propCardIdentity = $patientModel->prop_card_identity ?? [];
        $propCardIdentity['ihs_number'] = $ihsNumber;
        $patientModel->setAttribute('prop_card_identity', $propCardIdentity);

        $patientModel->save();
    }

    /**
     * Update patient sync counter for a specific sync type.
     *
     * @param mixed $patientModel
     * @param string $syncFlag Sync type flag (encounter, observation)
     * @param bool $incrementFrom Increment 'from' counter
     * @param bool $incrementTo Increment 'to' counter
     * @return void
     */
    protected function updatePatientSyncCounter($patientModel, string $syncFlag, bool $incrementFrom = true, bool $incrementTo = true): void
    {
        $integration = $this->initializePatientIntegration($patientModel);
        $satuSehat = &$integration['satu_sehat'];

        $syncLabel = null;
        $logFrom = $incrementFrom ? 1 : 0;
        $logTo = $incrementTo ? 1 : 0;

        // Update specific sync counter
        foreach ($satuSehat['syncs'] as &$sync) {
            if ($sync['flag'] === $syncFlag) {
                if ($incrementFrom) {
                    $sync['from'] = (int)($sync['from'] ?? 0) + 1;
                }
                if ($incrementTo) {
                    $sync['to'] = (int)($sync['to'] ?? 0) + 1;
                }
                $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] / $sync['to']) * 100, 2) : 0;
                $sync['last_updated_at'] = now()->format('Y-m-d H:i:s');
                $syncLabel = $sync['label'];
                break;
            }
        }

        // Add log entry for encounter and observation syncs (not patient)
        if (in_array($syncFlag, ['encounter', 'observation'])) {
            $satuSehat['logs'][] = [
                'flag' => $syncFlag,
                'label' => $syncLabel ?? ucfirst($syncFlag),
                'from' => $logFrom,
                'to' => $logTo,
                'progress' => $logTo > 0 ? round(($logFrom / $logTo) * 100, 2) : 0,
                'last_updated_at' => now()->format('Y-m-d H:i:s')
            ];
        }

        // Recalculate overall progress
        $satuSehat = $this->recalculatePatientProgress($satuSehat);

        $patientModel->setAttribute('integration', $integration);
        $patientModel->save();
    }

    /**
     * Update patient log entry for a specific type.
     *
     * @param mixed $patientModel
     * @param string $logFlag Log type flag (encounter, observation)
     * @param string $label Log label
     * @param bool $incrementFrom Increment 'from' counter
     * @param bool $incrementTo Increment 'to' counter
     * @return void
     */
    protected function updatePatientLog($patientModel, string $logFlag, string $label, bool $incrementFrom = true, bool $incrementTo = true): void
    {
        $integration = $this->initializePatientIntegration($patientModel);
        $satuSehat = &$integration['satu_sehat'];

        // Find or create log entry
        $logFound = false;
        foreach ($satuSehat['logs'] as &$log) {
            if ($log['flag'] === $logFlag) {
                if ($incrementFrom) {
                    $log['from'] = (int)($log['from'] ?? 0) + 1;
                }
                if ($incrementTo) {
                    $log['to'] = (int)($log['to'] ?? 0) + 1;
                }
                $log['progress'] = $log['to'] > 0 ? round(($log['from'] / $log['to']) * 100, 2) : 0;
                $log['last_updated_at'] = now()->format('Y-m-d H:i:s');
                $logFound = true;
                break;
            }
        }

        if (!$logFound) {
            $satuSehat['logs'][] = [
                'flag' => $logFlag,
                'label' => $label,
                'from' => $incrementFrom ? 1 : null,
                'to' => $incrementTo ? 1 : null,
                'progress' => $incrementTo ? ($incrementFrom ? 100 : 0) : 0,
                'last_updated_at' => now()->format('Y-m-d H:i:s')
            ];
        }

        $patientModel->setAttribute('integration', $integration);
        $patientModel->save();
    }

    /**
     * Update workspace IHS number and save.
     *
     * @param mixed $workspaceModel
     * @param string $ihsNumber
     * @return void
     */
    protected function updateWorkspaceIhsNumber($workspaceModel, string $ihsNumber): void
    {
        $integration = $this->initializeWorkspaceIntegration($workspaceModel);
        $integration['satu_sehat']['general']['ihs_number'] = $ihsNumber;

        $workspaceModel->setAttribute('integration', $integration);
        $workspaceModel->save();
    }

    /**
     * Update workspace sync counter for a specific sync type.
     *
     * @param mixed $workspaceModel
     * @param string $syncFlag Sync type flag (patient, encounter, observation)
     * @param bool $incrementFrom Increment 'from' counter
     * @param bool $incrementTo Increment 'to' counter
     * @return void
     */
    protected function updateWorkspaceSyncCounter($workspaceModel, string $syncFlag, bool $incrementFrom = true, bool $incrementTo = true): void
    {
        $integration = $this->initializeWorkspaceIntegration($workspaceModel);
        $satuSehat = &$integration['satu_sehat'];

        $syncLabel = null;
        $logFrom = $incrementFrom ? 1 : 0;
        $logTo = $incrementTo ? 1 : 0;

        // Update specific sync counter
        foreach ($satuSehat['syncs'] as &$sync) {
            if ($sync['flag'] === $syncFlag) {
                if ($incrementFrom) {
                    $sync['from'] = (int)($sync['from'] ?? 0) + 1;
                }
                if ($incrementTo) {
                    $sync['to'] = (int)($sync['to'] ?? 0) + 1;
                }
                $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] / $sync['to']) * 100, 2) : 0;
                $sync['last_updated_at'] = now()->format('Y-m-d H:i:s');
                $syncLabel = $sync['label'];
                break;
            }
        }

        // Add log entry for this sync operation
        $satuSehat['logs'][] = [
            'flag' => $syncFlag,
            'label' => $syncLabel ?? ucfirst($syncFlag),
            'from' => $logFrom,
            'to' => $logTo,
            'progress' => $logTo > 0 ? round(($logFrom / $logTo) * 100, 2) : 0,
            'last_updated_at' => now()->format('Y-m-d H:i:s')
        ];

        // Recalculate overall progress
        $satuSehat = $this->recalculateWorkspaceProgress($satuSehat);

        $workspaceModel->setAttribute('integration', $integration);
        $workspaceModel->save();
    }

    /**
     * Recalculate overall progress for workspace from syncs.
     *
     * @param array $satuSehat
     * @return array
     */
    protected function recalculateWorkspaceProgress(array $satuSehat): array
    {
        $totalFrom = 0;
        $totalTo = 0;

        foreach ($satuSehat['syncs'] ?? [] as $sync) {
            $totalFrom += (int)($sync['from'] ?? 0);
            $totalTo += (int)($sync['to'] ?? 0);
        }

        $satuSehat['progress'] = $totalTo > 0 ? round(($totalFrom / $totalTo) * 100, 2) : 0;

        return $satuSehat;
    }

    /**
     * Recalculate overall progress for patient from syncs.
     *
     * @param array $satuSehat
     * @return array
     */
    protected function recalculatePatientProgress(array $satuSehat): array
    {
        $totalFrom = 0;
        $totalTo = 0;

        foreach ($satuSehat['syncs'] ?? [] as $sync) {
            $totalFrom += (int)($sync['from'] ?? 0);
            $totalTo += (int)($sync['to'] ?? 0);
        }

        $satuSehat['progress'] = $totalTo > 0 ? round(($totalFrom / $totalTo) * 100, 2) : 0;
        $satuSehat['pending'] = $totalTo - $totalFrom;

        return $satuSehat;
    }

    /**
     * Update Elasticsearch dashboard metrics for workspace integration.
     *
     * @param string $syncFlag
     * @return void
     */
    protected function updateDashboardWorkspaceIntegration(string $syncFlag): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(DashboardMetricsService::class);
            $dashboardService->incrementWorkspaceSyncCounter($syncFlag);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update dashboard workspace integration', [
                'error' => $e->getMessage(),
                'sync_flag' => $syncFlag
            ]);
        }
    }

    /**
     * Update Elasticsearch dashboard metrics for patient integration.
     *
     * @param string $patientId
     * @param string $syncFlag
     * @return void
     */
    protected function updateDashboardPatientIntegration(string $patientId, string $syncFlag): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $patientService = app(PatientDashboardMetricsService::class);
            $patientService->incrementPatientSyncCounter($patientId, $syncFlag);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update dashboard patient integration', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'sync_flag' => $syncFlag
            ]);
        }
    }

    /**
     * Get workspace model from tenant reference.
     *
     * @return mixed|null
     */
    protected function getWorkspaceModel()
    {
        try {
            return tenancy()->tenant->reference ?? null;
        } catch (\Throwable $e) {
            Log::channel('satu-sehat')->warning('Failed to get workspace model', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
