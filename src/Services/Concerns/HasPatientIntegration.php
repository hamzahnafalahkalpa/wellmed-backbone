<?php

namespace Projects\WellmedBackbone\Services\Concerns;

use Illuminate\Support\Facades\Log;

/**
 * Trait HasPatientIntegration
 *
 * Handles patient-level integration tracking for external services like Satu Sehat.
 * Tracks sync status, progress, and integration health for individual patients.
 */
trait HasPatientIntegration
{
    /**
     * Sync statuses
     */
    public const SYNC_STATUS_PENDING = 'pending';
    public const SYNC_STATUS_IN_PROGRESS = 'in_progress';
    public const SYNC_STATUS_COMPLETED = 'completed';
    public const SYNC_STATUS_FAILED = 'failed';

    /**
     * Update patient sync status for Satu Sehat patient registration.
     *
     * @param string $patientId
     * @param string $status One of: pending, in_progress, completed, failed
     * @param string|null $ihsNumber The IHS number from Satu Sehat
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updatePatientSyncStatus(
        string $patientId,
        string $status,
        ?string $ihsNumber = null,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        return $this->updateIntegrationStatus(
            $patientId,
            'patient_registration',
            $status,
            ['ihs_number' => $ihsNumber],
            $tenantId,
            $workspaceId
        );
    }

    /**
     * Update sync counter for a specific sync type.
     *
     * @param string $patientId
     * @param string $syncFlag Sync type flag (encounter, dispense, condition, observation)
     * @param int $from Synced count
     * @param int $to Total count
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updatePatientSyncCounter(
        string $patientId,
        string $syncFlag,
        int $from,
        int $to,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($patientId, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['patient_integrations'])) {
                $currentData['patient_integrations'] = $this->getDefaultPatientIntegrations();
            }

            // Update specific sync counter
            $syncs = &$currentData['patient_integrations']['syncs'];
            foreach ($syncs as &$sync) {
                if ($sync['flag'] === $syncFlag) {
                    $sync['from'] = $from;
                    $sync['to'] = $to;
                    $sync['progress'] = $to > 0 ? round(($from / $to) * 100, 2) : 0;
                    $sync['last_updated_at'] = now()->format('Y-m-d H:i:s');
                    break;
                }
            }

            // Recalculate overall progress
            $currentData['patient_integrations'] = $this->recalculateOverallProgress($currentData['patient_integrations']);
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            return $this->storeCurrentPeriod($currentData, $patientId, $tenantId, $workspaceId, $timestamp);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update patient sync counter', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'sync_flag' => $syncFlag
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Increment sync counter for a specific sync type.
     *
     * @param string $patientId
     * @param string $syncFlag
     * @param bool $incrementTo Also increment 'to' counter
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function incrementPatientSyncCounter(
        string $patientId,
        string $syncFlag,
        bool $incrementTo = true,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($patientId, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['patient_integrations'])) {
                $currentData['patient_integrations'] = $this->getDefaultPatientIntegrations();
            }

            // Update specific sync counter
            $syncs = &$currentData['patient_integrations']['syncs'];
            foreach ($syncs as &$sync) {
                if ($sync['flag'] === $syncFlag) {
                    $sync['from'] = ($sync['from'] ?? 0) + 1;
                    if ($incrementTo) {
                        $sync['to'] = ($sync['to'] ?? 0) + 1;
                    }
                    $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] / $sync['to']) * 100, 2) : 0;
                    $sync['last_updated_at'] = now()->format('Y-m-d H:i:s');
                    break;
                }
            }

            // Recalculate overall progress
            $currentData['patient_integrations'] = $this->recalculateOverallProgress($currentData['patient_integrations']);
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            return $this->storeCurrentPeriod($currentData, $patientId, $tenantId, $workspaceId, $timestamp);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment patient sync counter', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'sync_flag' => $syncFlag
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Add a log entry for patient integration.
     *
     * @param string $patientId
     * @param string $logFlag
     * @param string $label
     * @param int $from
     * @param int $to
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function addPatientIntegrationLog(
        string $patientId,
        string $logFlag,
        string $label,
        int $from,
        int $to,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($patientId, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['patient_integrations'])) {
                $currentData['patient_integrations'] = $this->getDefaultPatientIntegrations();
            }

            // Find or add log entry
            $logs = &$currentData['patient_integrations']['logs'];
            $logFound = false;
            foreach ($logs as &$log) {
                if ($log['flag'] === $logFlag) {
                    $log['from'] = $from;
                    $log['to'] = $to;
                    $log['progress'] = $to > 0 ? round(($from / $to) * 100, 2) : 0;
                    $log['last_updated_at'] = now()->format('Y-m-d H:i:s');
                    $logFound = true;
                    break;
                }
            }

            if (!$logFound) {
                $logs[] = [
                    'flag' => $logFlag,
                    'label' => $label,
                    'progress' => $to > 0 ? round(($from / $to) * 100, 2) : 0,
                    'last_updated_at' => now()->format('Y-m-d H:i:s'),
                    'from' => $from,
                    'to' => $to
                ];
            }

            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            return $this->storeCurrentPeriod($currentData, $patientId, $tenantId, $workspaceId, $timestamp);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to add patient integration log', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'log_flag' => $logFlag
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update integration status in Elasticsearch for patient.
     *
     * @param string $patientId
     * @param string $syncType
     * @param string $status
     * @param array $additionalData
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    protected function updateIntegrationStatus(
        string $patientId,
        string $syncType,
        string $status,
        array $additionalData = [],
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($patientId, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['patient_integrations'])) {
                $currentData['patient_integrations'] = $this->getDefaultPatientIntegrations();
            }

            // Update IHS number if provided
            if (isset($additionalData['ihs_number']) && $additionalData['ihs_number']) {
                $currentData['patient_integrations']['general']['ihs_number'] = $additionalData['ihs_number'];
            }

            $currentData['patient_integrations']['last_updated_at'] = now()->format('Y-m-d H:i:s');
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            return $this->storeCurrentPeriod($currentData, $patientId, $tenantId, $workspaceId, $timestamp);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update patient integration status', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'sync_type' => $syncType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Recalculate overall progress from syncs.
     *
     * @param array $integrations
     * @return array
     */
    protected function recalculateOverallProgress(array $integrations): array
    {
        $totalFrom = 0;
        $totalTo = 0;

        foreach ($integrations['syncs'] ?? [] as $sync) {
            $totalFrom += $sync['from'] ?? 0;
            $totalTo += $sync['to'] ?? 0;
        }

        $integrations['from'] = $totalFrom;
        $integrations['to'] = $totalTo;
        $integrations['progress'] = $totalTo > 0 ? round(($totalFrom / $totalTo) * 100, 2) : 0;
        $integrations['last_updated_at'] = now()->format('Y-m-d H:i:s');

        return $integrations;
    }

    /**
     * Find integration index by type.
     *
     * @param array $integrations
     * @param string $type
     * @return int|null
     */
    protected function findIntegrationIndex(array $integrations, string $type): ?int
    {
        foreach ($integrations as $index => $integration) {
            if (($integration['type'] ?? $integration['flag'] ?? '') === $type) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Get default patient integrations structure with new payload format.
     *
     * @return array
     */
    protected function getDefaultPatientIntegrations(): array
    {
        return [
            'flag' => 'satu-sehat',
            'label' => 'Satu Sehat',
            'progress' => 0,
            'last_updated_at' => now()->format('Y-m-d H:i:s'),
            'from' => 0,
            'to' => 0,
            'general' => [
                'ihs_number' => null
            ],
            'syncs' => [
                [
                    'flag' => 'encounter',
                    'label' => 'Kunjungan',
                    'progress' => 0,
                    'last_updated_at' => now()->format('Y-m-d H:i:s'),
                    'from' => 0,
                    'to' => 0
                ],
                [
                    'flag' => 'dispense',
                    'label' => 'Resep',
                    'progress' => 0,
                    'last_updated_at' => now()->format('Y-m-d H:i:s'),
                    'from' => 0,
                    'to' => 0
                ],
                [
                    'flag' => 'condition',
                    'label' => 'Diagnosa',
                    'progress' => 0,
                    'last_updated_at' => now()->format('Y-m-d H:i:s'),
                    'from' => 0,
                    'to' => 0
                ]
            ],
            'logs' => []
        ];
    }

    /**
     * Get patient integration summary.
     *
     * @param string $patientId
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function getPatientIntegrationSummary(
        string $patientId,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($patientId, $tenantId, $workspaceId, $timestamp);

            return [
                'success' => true,
                'data' => $currentData['patient_integrations'] ?? $this->getDefaultPatientIntegrations()
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get patient integration summary', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
