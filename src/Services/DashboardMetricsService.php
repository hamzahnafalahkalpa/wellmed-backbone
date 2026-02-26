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
     * Create default document for a specific date.
     * Public wrapper for createDefaultDocument.
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    public function generateDefaultDocument(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        return $this->createDefaultDocument($periodType, $tenantId, $workspaceId, $timestamp);
    }

    /**
     * Generate document with random test data for testing purposes.
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    public function generateDocumentWithRandomData(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        try {
            $documentId = $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp);

            $this->ensureIndexExists($periodType);

            // Get base document structure
            $document = $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $timestamp);

            // Populate with random data
            $document['statistics'] = $this->generateRandomStatistics();
            $document['motivational_stats'] = $this->generateRandomMotivationalStats();
            $document['pending_items'] = $this->generateRandomPendingItems();
            $document['cashier'] = $this->generateRandomCashier();
            $document['billing'] = $this->generateRandomBilling();
            $document['trends'] = $this->generateRandomTrends($periodType, $timestamp);

            // Store document
            $response = $this->client->index([
                'index' => $this->getIndexName($periodType),
                'id' => $documentId,
                'body' => $document,
                'op_type' => 'create'
            ]);

            Log::channel('elasticsearch')->info('Created dashboard document with random data', [
                'document_id' => $documentId,
                'tenant_id' => $tenantId,
                'workspace_id' => $workspaceId,
                'date' => $timestamp->format('Y-m-d')
            ]);

            return ['success' => true, 'id' => $response['_id'] ?? null];

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'version_conflict_engine_exception') ||
                str_contains($e->getMessage(), 'document already exists')) {
                return ['success' => true, 'already_exists' => true];
            }

            Log::channel('elasticsearch')->error('Failed to create document with random data', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate random statistics data
     */
    protected function generateRandomStatistics(): array
    {
        $now = now()->toIso8601String();
        return [
            [
                'id' => 'patients',
                'count' => rand(80, 120),
                'previous_count' => rand(70, 110),
                'is_cumulative' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'new-patients',
                'count' => rand(0, 5),
                'previous_count' => rand(0, 5),
                'is_cumulative' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'revenue',
                'count' => rand(500000, 2000000),
                'previous_count' => rand(500000, 2000000),
                'is_cumulative' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'treatment',
                'count' => rand(10, 50),
                'previous_count' => rand(10, 50),
                'is_cumulative' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];
    }

    /**
     * Generate random motivational stats data
     */
    protected function generateRandomMotivationalStats(): array
    {
        $current = rand(1, 10);
        $target = rand(5, 15);
        $previousCurrent = rand(1, 10);

        return [
            'current' => $current,
            'target' => $target,
            'previous_current' => $previousCurrent,
            'previous_target' => $target,
            'percentage' => $target > 0 ? round(($current / $target) * 100) : 0,
            'remaining' => max(0, $target - $current),
            'growth' => $current - $previousCurrent,
            'growth_percentage' => $previousCurrent > 0 ? round((($current - $previousCurrent) / $previousCurrent) * 100) : 0
        ];
    }

    /**
     * Generate random pending items data
     */
    protected function generateRandomPendingItems(): array
    {
        $now = now()->toIso8601String();
        return [
            [
                'id' => 'unsigned-visits',
                'count' => rand(0, 5),
                'previous_count' => rand(0, 5),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'unsynced-patients',
                'count' => rand(0, 10),
                'previous_count' => rand(0, 10),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'incomplete-diagnosis',
                'count' => rand(0, 3),
                'previous_count' => rand(0, 3),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];
    }

    /**
     * Generate random cashier data
     */
    protected function generateRandomCashier(): array
    {
        $now = now()->toIso8601String();
        return [
            [
                'id' => 'revenue',
                'count' => rand(1000000, 5000000),
                'previous_count' => rand(1000000, 5000000),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'unpaid',
                'count' => rand(100000, 500000),
                'previous_count' => rand(100000, 500000),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'total-transactions',
                'count' => rand(10, 50),
                'previous_count' => rand(10, 50),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'pending',
                'count' => rand(0, 5),
                'previous_count' => rand(0, 5),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];
    }

    /**
     * Generate random billing data
     */
    protected function generateRandomBilling(): array
    {
        $now = now()->toIso8601String();
        return [
            [
                'id' => 'total-billing',
                'count' => rand(20, 100),
                'previous_count' => rand(20, 100),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'unpaid-billing',
                'count' => rand(0, 10),
                'previous_count' => rand(0, 10),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'paid-billing',
                'count' => rand(10, 90),
                'previous_count' => rand(10, 90),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'total-revenue',
                'count' => rand(2000000, 10000000),
                'previous_count' => rand(2000000, 10000000),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];
    }

    /**
     * Generate random trends data
     */
    protected function generateRandomTrends(string $periodType, Carbon $timestamp): array
    {
        $services = ['Umum', 'Gigi', 'Anak', 'Kebidanan'];
        $count = $this->getPeriodCount($periodType);

        $servicesData = [];
        $datasetSource = [];

        // Generate labels for x-axis
        $labels = ['Kunjungan'];
        $dates = [];

        for ($i = $count - 1; $i >= 0; $i--) {
            $periodTimestamp = match ($periodType) {
                self::PERIOD_DAILY => $timestamp->copy()->subDays($i),
                self::PERIOD_WEEKLY => $timestamp->copy()->subWeeks($i),
                self::PERIOD_MONTHLY => $timestamp->copy()->subMonths($i),
                self::PERIOD_YEARLY => $timestamp->copy()->subYears($i),
                default => $timestamp->copy()->subDays($i)
            };

            $dates[] = [
                'timestamp' => $periodTimestamp,
                'key' => $periodTimestamp->format('Y-m-d'),
                'label' => $this->getTrendPeriodLabel($periodType, $periodTimestamp)
            ];

            $labels[] = $this->getTrendPeriodLabel($periodType, $periodTimestamp);
        }
        $datasetSource[] = $labels;

        // Generate data for each service
        foreach ($services as $service) {
            $data = [];
            $serviceData = [strtoupper($service)];

            foreach ($dates as $dateInfo) {
                $count = rand(0, 20);

                $data[] = [
                    'key' => $dateInfo['key'],
                    'label' => $dateInfo['label'],
                    'count' => $count
                ];

                // Convert to string to avoid ES mapping conflict (mixed type array)
                $serviceData[] = (string) $count;
            }

            $servicesData[] = [
                'service' => $service,
                'service_label' => strtoupper($service),
                'data' => $data
            ];

            $datasetSource[] = $serviceData;
        }

        return [
            'services' => $servicesData,
            'dataset' => [
                'source' => $datasetSource
            ],
            'title' => 'Tren Kunjungan per Poliklinik',
            'subtitle' => $this->getTrendSubtitle($periodType),
            'chart_type' => 'line',
            'series_layout' => 'row',
            'period_type' => $periodType
        ];
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
