<?php

namespace Projects\WellmedBackbone\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Projects\WellmedBackbone\Services\Concerns\HasBilling;
use Projects\WellmedBackbone\Services\Concerns\HasCashier;
use Projects\WellmedBackbone\Services\Concerns\HasDashboardMetricsDefaults;
use Projects\WellmedBackbone\Services\Concerns\HasMotivationalStats;
use Projects\WellmedBackbone\Services\Concerns\HasPendingItem;
use Projects\WellmedBackbone\Services\Concerns\HasStatistic;
use Projects\WellmedBackbone\Services\Concerns\HasTreatmentDiagnose;
use Projects\WellmedBackbone\Services\Concerns\HasQueueService;
use Projects\WellmedBackbone\Services\Concerns\HasTrend;
use Projects\WellmedBackbone\Services\Concerns\HasWorkspaceIntegration;

/**
 * Dashboard Metrics Service
 *
 * Handles storage and retrieval of dashboard metrics data in Elasticsearch.
 * Business logic for each metric type is in the corresponding trait.
 */
class DashboardMetricsService
{
    use HasDashboardMetricsDefaults;
    use HasStatistic, HasPendingItem, HasCashier, HasBilling, HasMotivationalStats,
        HasTreatmentDiagnose, HasQueueService, HasTrend, HasWorkspaceIntegration;

    protected $client;
    protected string $indexPrefix = 'dashboard-metrics';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    /**
     * Get or create current period document.
     */
    protected function getOrCreateCurrentPeriod(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        $defaultDocument = $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $timestamp);

        try {
            $this->ensureIndexExists($periodType);

            $existingDoc = $this->getDocument($periodType, $tenantId, $workspaceId, $timestamp);

            if ($existingDoc) {
                return $this->mergeWithDefaults($existingDoc, $defaultDocument);
            }

            return $defaultDocument;
        } catch (\Throwable $e) {
            return $defaultDocument;
        }
    }

    /**
     * Merge existing document with defaults to ensure all fields exist.
     */
    protected function mergeWithDefaults(array $existing, array $defaults): array
    {
        $merged = $defaults;
        $periodType = $defaults['period_type'] ?? $existing['period_type'] ?? 'daily';

        // Preserve scalar values
        foreach (['tenant_id', 'workspace_id', 'period_type', 'timestamp', 'date', 'year', 'month', 'week', 'day'] as $key) {
            if (isset($existing[$key])) {
                $merged[$key] = $existing[$key];
            }
        }

        // Merge array fields by ID
        foreach (['statistics', 'pending_items', 'cashier', 'billing'] as $field) {
            if (isset($existing[$field]) && is_array($existing[$field])) {
                $merged[$field] = $this->mergeArrayById($defaults[$field] ?? [], $existing[$field]);
            }
        }

        // Merge simple object fields
        foreach (['motivational_stats', 'aggregation_period', 'metadata'] as $field) {
            if (isset($existing[$field]) && is_array($existing[$field])) {
                $merged[$field] = array_merge($defaults[$field] ?? [], $existing[$field]);
            }
        }

        // Preserve array fields as-is
        foreach (['queue_services', 'diagnosis_treatment', 'trends'] as $field) {
            if (isset($existing[$field])) {
                $merged[$field] = $existing[$field];
            }
        }

        // Special handling for workspace_integrations (single object structure)
        if (isset($existing['workspace_integrations']) && is_array($existing['workspace_integrations']) && !empty($existing['workspace_integrations'])) {
            $merged['workspace_integrations'] = $this->mergeWorkspaceIntegrations(
                $defaults['workspace_integrations'] ?? $this->getDefaultWorkspaceIntegrations($periodType),
                $existing['workspace_integrations']
            );
        } else {
            $merged['workspace_integrations'] = $defaults['workspace_integrations'] ?? $this->getDefaultWorkspaceIntegrations($periodType);
        }

        return $merged;
    }

    /**
     * Merge arrays by ID field.
     */
    protected function mergeArrayById(array $defaults, array $existing): array
    {
        $existingById = [];
        foreach ($existing as $item) {
            if (isset($item['id'])) {
                $existingById[$item['id']] = $item;
            }
        }

        $merged = [];
        foreach ($defaults as $defaultItem) {
            $id = $defaultItem['id'] ?? null;
            if ($id && isset($existingById[$id])) {
                $merged[] = array_merge($defaultItem, $existingById[$id]);
                unset($existingById[$id]);
            } else {
                $merged[] = $defaultItem;
            }
        }

        foreach ($existingById as $item) {
            $merged[] = $item;
        }

        return $merged;
    }

    /**
     * Merge workspace integrations object.
     */
    protected function mergeWorkspaceIntegrations(array $defaults, array $existing): array
    {
        $merged = $defaults;

        foreach (['flag', 'label', 'progress', 'last_updated_at', 'from', 'to'] as $key) {
            if (isset($existing[$key]) && $existing[$key] !== null) {
                $merged[$key] = $existing[$key];
            }
        }

        if (isset($existing['general']) && is_array($existing['general'])) {
            $merged['general'] = array_merge($defaults['general'] ?? [], $existing['general']);
        }

        if (isset($existing['syncs']) && is_array($existing['syncs']) && !empty($existing['syncs'])) {
            $existingByFlag = [];
            foreach ($existing['syncs'] as $sync) {
                if (isset($sync['flag'])) {
                    $existingByFlag[$sync['flag']] = $sync;
                }
            }

            $mergedSyncs = [];
            foreach ($defaults['syncs'] ?? [] as $defaultSync) {
                $flag = $defaultSync['flag'] ?? null;
                if ($flag && isset($existingByFlag[$flag])) {
                    $mergedSyncs[] = array_merge($defaultSync, array_filter($existingByFlag[$flag], fn($v) => $v !== null));
                    unset($existingByFlag[$flag]);
                } else {
                    $mergedSyncs[] = $defaultSync;
                }
            }
            foreach ($existingByFlag as $sync) {
                $mergedSyncs[] = $sync;
            }
            $merged['syncs'] = $mergedSyncs;
        }

        $merged['logs'] = $existing['logs'] ?? [];

        return $merged;
    }

    /**
     * Get previous period data for comparison.
     */
    protected function getPreviousPeriodData(string $periodType, int $tenantId, mixed $workspaceId, Carbon $currentTimestamp): array
    {
        $previousTimestamp = match ($periodType) {
            self::PERIOD_DAILY => $currentTimestamp->copy()->subDay(),
            self::PERIOD_WEEKLY => $currentTimestamp->copy()->subWeek(),
            self::PERIOD_MONTHLY => $currentTimestamp->copy()->subMonth(),
            self::PERIOD_YEARLY => $currentTimestamp->copy()->subYear(),
            default => $currentTimestamp->copy()->subDay()
        };

        $existingDoc = $this->getDocument($periodType, $tenantId, $workspaceId, $previousTimestamp);

        return $existingDoc ?? $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $previousTimestamp);
    }

    /**
     * Store current period document.
     * Alias for storeDocument from trait with additional logging.
     */
    protected function storeCurrentPeriod(array $data, string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        return $this->storeDocument($data, $periodType, $tenantId, $workspaceId, $timestamp);
    }

    /**
     * Retrieve dashboard metrics.
     */
    public function get(string $periodType = 'daily', array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $response = $this->client->search([
                'index' => $this->getIndexName($periodType),
                'body' => $this->buildQuery($periodType, $tenantId, $workspaceId, $filters),
                'size' => $filters['size'] ?? 1,
                'sort' => ['timestamp:desc']
            ]);

            if (empty($response['hits']['hits'])) {
                return ['success' => true, 'data' => null, 'message' => 'No data found'];
            }

            return [
                'success' => true,
                'data' => $response['hits']['hits'][0]['_source'],
                'total' => $response['hits']['total']['value'] ?? 0
            ];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to retrieve dashboard metrics', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function buildQuery(string $periodType, int $tenantId, mixed $workspaceId, array $filters): array
    {
        $must = [
            ['term' => ['tenant_id' => $tenantId]],
            ['term' => ['period_type' => $periodType]]
        ];

        if ($workspaceId) {
            $must[] = ['term' => ['workspace_id' => $workspaceId]];
        }

        foreach (['date', 'year', 'month', 'week', 'day'] as $field) {
            if (isset($filters[$field])) {
                $must[] = ['term' => [$field => $filters[$field]]];
            }
        }

        return ['query' => ['bool' => ['must' => $must]]];
    }

    /**
     * Delete metrics by filters.
     */
    public function delete(string $periodType, array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $response = $this->client->deleteByQuery([
                'index' => $this->getIndexName($periodType),
                'body' => $this->buildQuery($periodType, $tenantId, $workspaceId, $filters)
            ]);

            return ['success' => true, 'deleted' => $response['deleted'] ?? 0];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to delete dashboard metrics', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
