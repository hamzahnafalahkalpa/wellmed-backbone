<?php

namespace Projects\WellmedBackbone\Services\Concerns;

use Illuminate\Support\Facades\Log;

/**
 * Trait HasWorkspaceIntegration
 *
 * Handles workspace-level integration tracking for external services like Satu Sehat.
 * Tracks sync status, progress, and integration health for workspace/organization.
 */
trait HasWorkspaceIntegration
{
    /**
     * Integration types supported
     */
    public const INTEGRATION_SATU_SEHAT = 'satu_sehat';

    /**
     * Sync statuses
     */
    public const SYNC_STATUS_PENDING = 'pending';
    public const SYNC_STATUS_IN_PROGRESS = 'in_progress';
    public const SYNC_STATUS_COMPLETED = 'completed';
    public const SYNC_STATUS_FAILED = 'failed';

    /**
     * Update workspace integration status for Satu Sehat organization sync.
     *
     * @param string $status One of: pending, in_progress, completed, failed
     * @param string|null $ihsNumber The IHS number from Satu Sehat
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateOrganizationSyncStatus(
        string $status,
        ?string $ihsNumber = null,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        return $this->updateWorkspaceIntegrationStatus(
            'organization',
            $status,
            ['ihs_number' => $ihsNumber],
            $tenantId,
            $workspaceId
        );
    }

    /**
     * Update workspace integration status for Satu Sehat location sync.
     *
     * @param string $status
     * @param int $syncedCount
     * @param int $totalCount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateLocationSyncStatus(
        string $status,
        int $syncedCount = 0,
        int $totalCount = 0,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        return $this->updateWorkspaceIntegrationStatus(
            'location',
            $status,
            [
                'synced_count' => $syncedCount,
                'total_count' => $totalCount,
                'progress' => $totalCount > 0 ? round(($syncedCount / $totalCount) * 100, 2) : 0
            ],
            $tenantId,
            $workspaceId
        );
    }

    /**
     * Update workspace integration status for Satu Sehat practitioner sync.
     *
     * @param string $status
     * @param int $syncedCount
     * @param int $totalCount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updatePractitionerSyncStatus(
        string $status,
        int $syncedCount = 0,
        int $totalCount = 0,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        return $this->updateWorkspaceIntegrationStatus(
            'practitioner',
            $status,
            [
                'synced_count' => $syncedCount,
                'total_count' => $totalCount,
                'progress' => $totalCount > 0 ? round(($syncedCount / $totalCount) * 100, 2) : 0
            ],
            $tenantId,
            $workspaceId
        );
    }

    /**
     * Update sync counter for a specific sync type in workspace.
     *
     * @param string $syncFlag Sync type flag (encounter, dispense, condition, patient)
     * @param int $from Synced count
     * @param int $to Total count
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateWorkspaceSyncCounter(
        string $syncFlag,
        int $from,
        int $to,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            try {
                $tenantId = $tenantId ?? tenancy()->tenant->getKey();
                $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
                $timestamp = now();

                $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

                if (!isset($currentData['workspace_integrations'])) {
                    $currentData['workspace_integrations'] = $this->getDefaultWorkspaceIntegrations($periodType);
                }

                // Update specific sync counter
                $syncs = &$currentData['workspace_integrations']['syncs'];
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
                $currentData['workspace_integrations'] = $this->recalculateWorkspaceOverallProgress($currentData['workspace_integrations']);
                $currentData['metadata']['updated_at'] = now()->toIso8601String();

                $results[$periodType] = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            } catch (\Throwable $e) {
                Log::channel('elasticsearch')->error('Failed to update workspace sync counter', [
                    'error' => $e->getMessage(),
                    'sync_flag' => $syncFlag,
                    'period_type' => $periodType
                ]);

                $results[$periodType] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Increment sync counter for a specific sync type in workspace.
     *
     * @param string $syncFlag
     * @param bool $incrementTo Also increment 'to' counter
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function incrementWorkspaceSyncCounter(
        string $syncFlag,
        bool $incrementTo = true,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            try {
                $tenantId = $tenantId ?? tenancy()->tenant->getKey();
                $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
                $timestamp = now();

                $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

                if (!isset($currentData['workspace_integrations'])) {
                    $currentData['workspace_integrations'] = $this->getDefaultWorkspaceIntegrations($periodType);
                }

                // Update specific sync counter
                $syncs = &$currentData['workspace_integrations']['syncs'];
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
                $currentData['workspace_integrations'] = $this->recalculateWorkspaceOverallProgress($currentData['workspace_integrations']);
                $currentData['metadata']['updated_at'] = now()->toIso8601String();

                $results[$periodType] = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            } catch (\Throwable $e) {
                Log::channel('elasticsearch')->error('Failed to increment workspace sync counter', [
                    'error' => $e->getMessage(),
                    'sync_flag' => $syncFlag,
                    'period_type' => $periodType
                ]);

                $results[$periodType] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Update integration status in Elasticsearch for workspace.
     *
     * @param string $syncType
     * @param string $status
     * @param array $additionalData
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    protected function updateWorkspaceIntegrationStatus(
        string $syncType,
        string $status,
        array $additionalData = [],
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            try {
                $tenantId = $tenantId ?? tenancy()->tenant->getKey();
                $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
                $timestamp = now();

                $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

                if (!isset($currentData['workspace_integrations'])) {
                    $currentData['workspace_integrations'] = $this->getDefaultWorkspaceIntegrations($periodType);
                }

                // Update IHS number if provided
                if (isset($additionalData['ihs_number']) && $additionalData['ihs_number']) {
                    $currentData['workspace_integrations']['general']['ihs_number'] = $additionalData['ihs_number'];
                }

                // Update sync type if progress data provided
                if (isset($additionalData['synced_count']) && isset($additionalData['total_count'])) {
                    $syncs = &$currentData['workspace_integrations']['syncs'];
                    foreach ($syncs as &$sync) {
                        if ($sync['flag'] === $syncType) {
                            $sync['from'] = $additionalData['synced_count'];
                            $sync['to'] = $additionalData['total_count'];
                            $sync['progress'] = $additionalData['progress'] ?? 0;
                            $sync['last_updated_at'] = now()->format('Y-m-d H:i:s');
                            break;
                        }
                    }

                    // Recalculate overall progress
                    $currentData['workspace_integrations'] = $this->recalculateWorkspaceOverallProgress($currentData['workspace_integrations']);
                }

                $currentData['workspace_integrations']['last_updated_at'] = now()->format('Y-m-d H:i:s');
                $currentData['metadata']['updated_at'] = now()->toIso8601String();

                $results[$periodType] = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            } catch (\Throwable $e) {
                Log::channel('elasticsearch')->error('Failed to update workspace integration status', [
                    'error' => $e->getMessage(),
                    'sync_type' => $syncType,
                    'period_type' => $periodType
                ]);

                $results[$periodType] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Increment failed sync counter for workspace integration.
     *
     * @param string $syncType Type of sync (organization, location, practitioner, patient)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementFailedSync(string $syncType, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->updateIntegrationCounter(
                $syncType,
                'failed_count',
                1,
                $periodType,
                $tenantId,
                $workspaceId
            );
        }
        return $results;
    }

    /**
     * Increment successful sync counter for workspace integration.
     *
     * @param string $syncType
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function incrementSuccessSync(string $syncType, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->updateIntegrationCounter(
                $syncType,
                'success_count',
                1,
                $periodType,
                $tenantId,
                $workspaceId
            );
        }
        return $results;
    }

    /**
     * Update integration counter.
     *
     * @param string $syncType
     * @param string $counterKey
     * @param int $increment
     * @param string $periodType
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    protected function updateIntegrationCounter(
        string $syncType,
        string $counterKey,
        int $increment,
        string $periodType,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['workspace_integrations'])) {
                $currentData['workspace_integrations'] = $this->getDefaultWorkspaceIntegrations($periodType);
            }

            $syncs = &$currentData['workspace_integrations']['syncs'];
            foreach ($syncs as &$sync) {
                if ($sync['flag'] === $syncType) {
                    $currentCount = $sync[$counterKey] ?? 0;
                    $sync[$counterKey] = $currentCount + $increment;
                    $sync['last_updated_at'] = now()->format('Y-m-d H:i:s');
                    break;
                }
            }

            $currentData['workspace_integrations']['last_updated_at'] = now()->format('Y-m-d H:i:s');
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            return $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update integration counter', [
                'error' => $e->getMessage(),
                'sync_type' => $syncType,
                'counter_key' => $counterKey
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Recalculate overall progress for workspace from syncs.
     *
     * @param array $integrations
     * @return array
     */
    protected function recalculateWorkspaceOverallProgress(array $integrations): array
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
     * Get workspace integration summary.
     *
     * @param string $periodType
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function getWorkspaceIntegrationSummary(
        string $periodType = 'daily',
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            return [
                'success' => true,
                'data' => $currentData['workspace_integrations'] ?? $this->getDefaultWorkspaceIntegrations($periodType)
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get workspace integration summary', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
