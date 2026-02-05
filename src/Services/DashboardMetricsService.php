<?php

namespace Projects\WellmedBackbone\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Projects\WellmedBackbone\Services\Concerns\HasBilling;
use Projects\WellmedBackbone\Services\Concerns\HasCashier;
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
    use HasStatistic, HasPendingItem, HasCashier, HasBilling, HasMotivationalStats,
        HasTreatmentDiagnose, HasQueueService, HasTrend, HasWorkspaceIntegration;

    protected $client;
    protected string $indexPrefix = 'dashboard-metrics';

    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_YEARLY = 'yearly';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    public function getPeriodTypes(): array
    {
        return [self::PERIOD_DAILY, self::PERIOD_WEEKLY, self::PERIOD_MONTHLY, self::PERIOD_YEARLY];
    }

    /**
     * Get or create current period document.
     */
    protected function getOrCreateCurrentPeriod(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        $documentId = $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp);
        $defaultDocument = $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $timestamp);

        try {
            $this->ensureIndexExists($periodType);
            $response = $this->client->get([
                'index' => $this->getIndexName($periodType),
                'id' => $documentId
            ]);
            return $this->mergeWithDefaults($response['_source'], $defaultDocument);
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

        try {
            $response = $this->client->get([
                'index' => $this->getIndexName($periodType),
                'id' => $this->generateDocumentId($periodType, $tenantId, $workspaceId, $previousTimestamp)
            ]);
            return $response['_source'];
        } catch (\Throwable $e) {
            return $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $previousTimestamp);
        }
    }

    /**
     * Store current period document.
     */
    protected function storeCurrentPeriod(array $data, string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        try {
            $this->ensureIndexExists($periodType);

            // Ensure workspace_integrations is never null
            if (!isset($data['workspace_integrations']) || !is_array($data['workspace_integrations'])) {
                $data['workspace_integrations'] = $this->getDefaultWorkspaceIntegrations($periodType);
            }

            $response = $this->client->index([
                'index' => $this->getIndexName($periodType),
                'id' => $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp),
                'body' => $data
            ]);

            return ['success' => true, 'id' => $response['_id'] ?? null, 'index' => $response['_index'] ?? null];
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store current period', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate document ID for a period.
     */
    protected function generateDocumentId(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): string
    {
        $periodKey = match ($periodType) {
            self::PERIOD_DAILY => $timestamp->format('Y-m-d'),
            self::PERIOD_WEEKLY => $timestamp->format('Y') . '-W' . $timestamp->format('W'),
            self::PERIOD_MONTHLY => $timestamp->format('Y-m'),
            self::PERIOD_YEARLY => $timestamp->format('Y'),
            default => $timestamp->format('Y-m-d')
        };
        return "{$periodType}_{$tenantId}_{$workspaceId}_{$periodKey}";
    }

    /**
     * Get default document structure.
     */
    protected function getDefaultDocument(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        return [
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'period_type' => $periodType,
            'timestamp' => $timestamp->toIso8601String(),
            'date' => $timestamp->toDateString(),
            'year' => $timestamp->year,
            'month' => $timestamp->month,
            'week' => (int) $timestamp->format('W'),
            'day' => $timestamp->day,
            'statistics' => $this->getDefaultStatistics($periodType),
            'motivational_stats' => [
                'current' => 0, 'target' => 0, 'percentage' => 0,
                'remaining' => 0, 'growth' => 0, 'growth_percentage' => 0
            ],
            'pending_items' => $this->getDefaultPendingItems($periodType),
            'cashier' => $this->getDefaultCashier($periodType),
            'billing' => $this->getDefaultBilling($periodType),
            'queue_services' => [],
            'diagnosis_treatment' => [],
            'workspace_integrations' => $this->getDefaultWorkspaceIntegrations($periodType),
            'trends' => $this->getDefaultTrends($periodType, $timestamp),
            'aggregation_period' => [
                'start_date' => $timestamp->toDateString(),
                'end_date' => $timestamp->toDateString(),
                'label' => $this->getPeriodLabel($periodType, $timestamp)
            ],
            'metadata' => [
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
                'created_by' => 'system',
                'version' => '1.0'
            ]
        ];
    }

    protected function getChangeLabel(string $periodType): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => 'Dari kemarin',
            self::PERIOD_WEEKLY => 'Dari minggu lalu',
            self::PERIOD_MONTHLY => 'Dari bulan lalu',
            self::PERIOD_YEARLY => 'Dari tahun lalu',
            default => 'Dari kemarin'
        };
    }

    protected function getPeriodLabel(string $periodType, Carbon $timestamp): string
    {
        return match ($periodType) {
            'daily' => $timestamp->format('d M Y'),
            'weekly' => 'Minggu ' . $timestamp->format('W, Y'),
            'monthly' => $timestamp->format('F Y'),
            'yearly' => $timestamp->format('Y'),
            default => $timestamp->toDateString()
        };
    }

    /**
     * Ensure index exists, create if not.
     */
    protected function ensureIndexExists(string $periodType): bool
    {
        try {
            $indexName = $this->getIndexName($periodType);
            if (!$this->client->indices()->exists(['index' => $indexName])) {
                $this->client->indices()->create([
                    'index' => $indexName,
                    'body' => [
                        'settings' => ['number_of_shards' => 1, 'number_of_replicas' => 0],
                        'mappings' => [
                            'properties' => [
                                'tenant_id' => ['type' => 'integer'],
                                'workspace_id' => ['type' => 'integer'],
                                'period_type' => ['type' => 'keyword'],
                                'timestamp' => ['type' => 'date'],
                                'date' => ['type' => 'date', 'format' => 'yyyy-MM-dd'],
                                'year' => ['type' => 'integer'],
                                'month' => ['type' => 'integer'],
                                'week' => ['type' => 'integer'],
                                'day' => ['type' => 'integer'],
                                'statistics' => ['type' => 'nested'],
                                'motivational_stats' => ['type' => 'object'],
                                'pending_items' => ['type' => 'nested'],
                                'cashier' => ['type' => 'nested'],
                                'billing' => ['type' => 'nested'],
                                'queue_services' => ['type' => 'nested'],
                                'diagnosis_treatment' => ['type' => 'nested'],
                                'workspace_integrations' => ['type' => 'object'],
                                'trends' => ['type' => 'object'],
                                'aggregation_period' => ['type' => 'object'],
                                'metadata' => ['type' => 'object']
                            ]
                        ]
                    ]
                ]);
                Log::channel('elasticsearch')->info('Created dashboard metrics index', ['index' => $indexName]);
            }
            return true;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to ensure index exists', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);
            return true;
        }
    }

    protected function getIndexName(string $periodType): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');
        return $prefix . $separator . $this->indexPrefix . '-' . $periodType;
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
