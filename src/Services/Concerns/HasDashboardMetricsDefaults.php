<?php

namespace Projects\WellmedBackbone\Services\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Shared trait for dashboard metrics defaults.
 * Used by both Gateway (read-only) and Backbone (read-write) services.
 *
 * IMPORTANT: This trait now returns ES-only data (minimal structure).
 * Presentation data (labels, icons, colors) should be added by Resources
 * in the application layer (e.g., wellmed-backbone).
 *
 * Requirements:
 * - Implementing class must have a `$client` property with Elasticsearch client
 * - Implementing class must have a `$indexPrefix` property (default: 'dashboard-metrics')
 */
trait HasDashboardMetricsDefaults
{
    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_YEARLY = 'yearly';

    /**
     * Get all available period types.
     */
    public function getPeriodTypes(): array
    {
        return [self::PERIOD_DAILY, self::PERIOD_WEEKLY, self::PERIOD_MONTHLY, self::PERIOD_YEARLY];
    }

    /**
     * The number of periods to show for each period type.
     */
    protected function getPeriodCount(string $periodType): int
    {
        return match ($periodType) {
            self::PERIOD_DAILY => 7,    // 7 days
            self::PERIOD_WEEKLY => 4,   // 4 weeks
            self::PERIOD_MONTHLY => 12, // 12 months
            self::PERIOD_YEARLY => 5,   // 5 years
            default => 7
        };
    }

    /**
     * Get change label based on period type.
     *
     * Note: This is kept for backwards compatibility but presentation
     * labels should be handled by Resources in the application layer.
     */
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

    /**
     * Get period label for display.
     */
    protected function getPeriodLabel(string $periodType, Carbon $timestamp): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => $timestamp->format('d M Y'),
            self::PERIOD_WEEKLY => 'Minggu ' . $timestamp->format('W, Y'),
            self::PERIOD_MONTHLY => $timestamp->format('F Y'),
            self::PERIOD_YEARLY => $timestamp->format('Y'),
            default => $timestamp->toDateString()
        };
    }

    /**
     * Get default motivational stats structure.
     *
     * Contains current, target, and previous_current for comparison.
     * The transformer calculates percentage, remaining, growth, growth_percentage.
     */
    protected function getDefaultMotivationalStats(): array
    {
        return [
            'current' => 0,
            'target' => 10, // Default target
            'previous_current' => 0, // Previous period's current value for comparison
            'previous_target' => 0,
        ];
    }

    /**
     * Get default statistics array - ES-only data structure.
     *
     * Contains: id, count, previous_count, created_at, updated_at
     * The transformer calculates change, change_type, percentage_change from count and previous_count.
     * Presentation data (label, icon, color, etc.) is added by Resources.
     */
    protected function getDefaultStatistics(string $periodType, ?array $data = []): array
    {
        $now = now()->toIso8601String();

        $response = [
            [
                'id' => 'patients',
                'count' => 0,
                'previous_count' => 0,
                'is_cumulative' => true, // Total patients is cumulative
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'new-patients',
                'count' => 0,
                'previous_count' => 0,
                'is_cumulative' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'revenue',
                'count' => 0,
                'previous_count' => 0,
                'is_cumulative' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'treatment',
                'count' => 0,
                'previous_count' => 0,
                'is_cumulative' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        if (count($data) > 0) {
            foreach ($response as &$resp) {
                $id = Str::snake($resp['id']);
                if (isset($data[$id])) {
                    foreach ($data[$id] as $key => $dataItem) {
                        $resp[$key] = $dataItem;
                    }
                    $resp['updated_at'] = now()->toIso8601String();
                }
            }
        }

        return $response;
    }

    /**
     * Get default pending items structure - ES-only data.
     *
     * Contains: id, count, previous_count, created_at, updated_at
     * Presentation data (label, icon, color, link) is added by Resources.
     */
    protected function getDefaultPendingItems(string $periodType, ?array $data = []): array
    {
        $now = now()->toIso8601String();

        $response = [
            [
                'id' => 'unsigned-visits',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'unsynced-patients',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'incomplete-diagnosis',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        if (count($data) > 0) {
            foreach ($response as &$resp) {
                $id = Str::snake($resp['id']);
                if (isset($data[$id])) {
                    foreach ($data[$id] as $key => $dataItem) {
                        $resp[$key] = $dataItem;
                    }
                    $resp['updated_at'] = now()->toIso8601String();
                }
            }
        }

        return $response;
    }

    /**
     * Get default cashier structure - ES-only data.
     *
     * Contains: id, count, previous_count, created_at, updated_at
     * The transformer calculates change, change_type, percentage_change from count and previous_count.
     * Presentation data is added by Resources.
     */
    protected function getDefaultCashier(string $periodType, ?array $data = []): array
    {
        $now = now()->toIso8601String();

        $response = [
            [
                'id' => 'revenue',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'unpaid',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'total-transactions',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'pending',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        if (count($data) > 0) {
            foreach ($response as &$resp) {
                $id = Str::snake($resp['id']);
                if (isset($data[$id])) {
                    foreach ($data[$id] as $key => $dataItem) {
                        $resp[$key] = $dataItem;
                    }
                    $resp['updated_at'] = now()->toIso8601String();
                }
            }
        }

        return $response;
    }

    /**
     * Get default billing structure - ES-only data.
     *
     * Contains: id, count, previous_count, created_at, updated_at
     * The transformer calculates change, change_type, percentage_change from count and previous_count.
     * Presentation data is added by Resources.
     */
    protected function getDefaultBilling(string $periodType, ?array $data = []): array
    {
        $now = now()->toIso8601String();

        $response = [
            [
                'id' => 'total-billing',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'unpaid-billing',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'paid-billing',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'total-revenue',
                'count' => 0,
                'previous_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        if (count($data) > 0) {
            foreach ($response as &$resp) {
                $id = Str::snake($resp['id']);
                if (isset($data[$id])) {
                    foreach ($data[$id] as $key => $dataItem) {
                        $resp[$key] = $dataItem;
                    }
                    $resp['updated_at'] = now()->toIso8601String();
                }
            }
        }

        return $response;
    }

    /**
     * Get default workspace integrations structure - ES-only data.
     *
     * Contains only essential sync data without presentation labels.
     * Presentation data is added by Resources.
     */
    protected function getDefaultWorkspaceIntegrations(string $periodType): array
    {
        $now = now()->toIso8601String();
        $nowFormatted = now()->format('Y-m-d H:i:s');

        return [
            'flag' => 'satu-sehat',
            'progress' => 0,
            'last_updated_at' => $nowFormatted,
            'from' => 0,
            'to' => 0,
            'general' => [
                'ihs_number' => null
            ],
            'syncs' => [
                [
                    'flag' => 'encounter',
                    'progress' => 0,
                    'last_updated_at' => $nowFormatted,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'flag' => 'dispense',
                    'progress' => 0,
                    'last_updated_at' => $nowFormatted,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'flag' => 'condition',
                    'progress' => 0,
                    'last_updated_at' => $nowFormatted,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'flag' => 'patient',
                    'progress' => 0,
                    'last_updated_at' => $nowFormatted,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'flag' => 'location',
                    'progress' => 0,
                    'last_updated_at' => $nowFormatted,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'flag' => 'practitioner',
                    'progress' => 0,
                    'last_updated_at' => $nowFormatted,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ],
            'logs' => [],
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * Get default trends structure.
     */
    protected function getDefaultTrends(string $periodType, Carbon $timestamp): array
    {
        return [
            'services' => [],
            'dataset' => [
                'source' => [
                    $this->getTrendLabels($periodType, $timestamp)
                ]
            ],
            'title' => 'Tren Kunjungan per Poliklinik',
            'subtitle' => $this->getTrendSubtitle($periodType),
            'chart_type' => 'line',
            'series_layout' => 'row',
            'period_type' => $periodType
        ];
    }

    /**
     * Get x-axis labels for the trend chart.
     */
    protected function getTrendLabels(string $periodType, Carbon $timestamp): array
    {
        $labels = ['Kunjungan']; // First element is the header
        $count = $this->getPeriodCount($periodType);
        $now = Carbon::now();

        for ($i = $count - 1; $i >= 0; $i--) {
            $periodTimestamp = match ($periodType) {
                self::PERIOD_DAILY => $now->copy()->subDays($i),
                self::PERIOD_WEEKLY => $now->copy()->subWeeks($i),
                self::PERIOD_MONTHLY => $now->copy()->subMonths($i),
                self::PERIOD_YEARLY => $now->copy()->subYears($i),
                default => $now->copy()->subDays($i)
            };

            $labels[] = $this->getTrendPeriodLabel($periodType, $periodTimestamp);
        }

        return $labels;
    }

    /**
     * Get label for a specific trend period.
     */
    protected function getTrendPeriodLabel(string $periodType, Carbon $timestamp): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => $timestamp->format('d M'),
            self::PERIOD_WEEKLY => 'W' . $timestamp->format('W'),
            self::PERIOD_MONTHLY => $timestamp->format('M Y'),
            self::PERIOD_YEARLY => $timestamp->format('Y'),
            default => $timestamp->format('d M')
        };
    }

    /**
     * Get subtitle for trend chart.
     */
    protected function getTrendSubtitle(string $periodType): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => 'Berdasarkan 7 hari terakhir',
            self::PERIOD_WEEKLY => 'Berdasarkan 4 minggu terakhir',
            self::PERIOD_MONTHLY => 'Berdasarkan 12 bulan terakhir',
            self::PERIOD_YEARLY => 'Berdasarkan 5 tahun terakhir',
            default => ''
        };
    }

    /**
     * Get Elasticsearch index name.
     */
    protected function getIndexName(string $periodType): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');
        return $prefix . $separator . $this->indexPrefix . '-' . $periodType;
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
     * Get default document structure for dashboard metrics (ES-only data).
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
            'motivational_stats' => $this->getDefaultMotivationalStats(),
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
                'version' => '2.0' // Updated version for ES-only data structure
            ]
        ];
    }

    /**
     * Format ES response to standard dashboard response structure.
     *
     * Note: This returns ES-only data. Presentation data should be added
     * by transforming with Resources in the application layer.
     */
    protected function formatDashboardResponse(array $response, array $params): array
    {
        $now = Carbon::now();
        $periodType = $params['search_type'] ?? self::PERIOD_DAILY;

        // No data found - return defaults
        if (empty($response['hits']['hits'])) {
            return $this->getDefaultDashboardResponse($periodType, $now, $params);
        }

        $hit = $response['hits']['hits'][0]['_source'];

        // Return ES data with fallbacks for missing fields
        return [
            'motivational_stats' => $hit['motivational_stats'] ?? $this->getDefaultMotivationalStats(),
            'statistics' => $hit['statistics'] ?? $this->getDefaultStatistics($periodType),
            'pending_items' => $hit['pending_items'] ?? $this->getDefaultPendingItems($periodType),
            'cashier' => $hit['cashier'] ?? $this->getDefaultCashier($periodType),
            'billing' => $hit['billing'] ?? $this->getDefaultBilling($periodType),
            'queue_services' => $hit['queue_services'] ?? [],
            'diagnosis_treatment' => $hit['diagnosis_treatment'] ?? [],
            'workspace_integrations' => $hit['workspace_integrations'] ?? $this->getDefaultWorkspaceIntegrations($periodType),
            'trends' => $hit['trends'] ?? $this->getDefaultTrends($periodType, $now),
            'meta' => [
                'period_type' => $hit['period_type'] ?? $periodType,
                'timestamp' => $hit['timestamp'] ?? $now->toIso8601String(),
                'date' => $hit['date'] ?? $now->format('Y-m-d'),
                'year' => $hit['year'] ?? $now->year,
                'month' => $hit['month'] ?? $now->month,
                'week' => $hit['week'] ?? (int) $now->format('W'),
                'day' => $hit['day'] ?? $now->day,
                'data_source' => 'elasticsearch',
                'aggregation_period' => $hit['aggregation_period'] ?? null,
            ],
        ];
    }

    /**
     * Get default dashboard response when no data exists.
     */
    protected function getDefaultDashboardResponse(string $periodType, Carbon $now, array $params = []): array
    {
        return [
            'motivational_stats' => $this->getDefaultMotivationalStats(),
            'statistics' => $this->getDefaultStatistics($periodType),
            'pending_items' => $this->getDefaultPendingItems($periodType),
            'cashier' => $this->getDefaultCashier($periodType),
            'billing' => $this->getDefaultBilling($periodType),
            'queue_services' => [],
            'diagnosis_treatment' => [],
            'workspace_integrations' => $this->getDefaultWorkspaceIntegrations($periodType),
            'trends' => $this->getDefaultTrends($periodType, $now),
            'meta' => [
                'period_type' => $periodType,
                'message' => 'No data available for the specified period',
                'timestamp' => $now->toIso8601String(),
                'date' => $now->format('Y-m-d'),
                'year' => $now->year,
                'month' => $now->month,
                'week' => (int) $now->format('W'),
                'day' => $now->day,
                'data_source' => 'elasticsearch',
            ],
        ];
    }

    /**
     * Get timestamp for query based on params.
     */
    protected function getTimestampFromParams(array $params): Carbon
    {
        $periodType = $params['search_type'] ?? self::PERIOD_DAILY;
        $now = Carbon::now();

        return match ($periodType) {
            self::PERIOD_DAILY => !empty($params['search_date'])
                ? Carbon::parse($params['search_date'])
                : $now,
            self::PERIOD_WEEKLY => !empty($params['search_year']) && !empty($params['search_week'])
                ? Carbon::now()->setISODate((int) $params['search_year'], (int) $params['search_week'])
                : $now,
            self::PERIOD_MONTHLY => !empty($params['search_year']) && !empty($params['search_month'])
                ? Carbon::createFromDate((int) $params['search_year'], (int) $params['search_month'], 1)
                : $now,
            self::PERIOD_YEARLY => !empty($params['search_year'])
                ? Carbon::createFromDate((int) $params['search_year'], 1, 1)
                : $now,
            default => $now
        };
    }

    // =========================================================================
    // Elasticsearch Operations
    // =========================================================================

    /**
     * Ensure Elasticsearch index exists, create if not.
     * Uses the standard dashboard metrics mappings.
     */
    protected function ensureIndexExists(string $periodType): bool
    {
        try {
            $indexName = $this->getIndexName($periodType);

            // Check if index exists - handle different client response types
            $exists = $this->client->indices()->exists(['index' => $indexName]);
            $indexExists = is_bool($exists) ? $exists : (method_exists($exists, 'asBool') ? $exists->asBool() : (bool) $exists);

            if (!$indexExists) {
                $this->client->indices()->create([
                    'index' => $indexName,
                    'body' => [
                        'settings' => $this->getIndexSettings(),
                        'mappings' => ['properties' => $this->getIndexMappings()]
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
            return true; // Return true to allow operation to continue
        }
    }

    /**
     * Get Elasticsearch index settings.
     */
    protected function getIndexSettings(): array
    {
        return [
            'number_of_shards' => 1,
            'number_of_replicas' => 0
        ];
    }

    /**
     * Get Elasticsearch index mappings for dashboard metrics.
     */
    protected function getIndexMappings(): array
    {
        return [
            'tenant_id' => ['type' => 'integer'],
            'workspace_id' => ['type' => 'keyword'],
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
        ];
    }

    /**
     * Create default document in Elasticsearch if it doesn't exist.
     * Uses op_type 'create' to prevent overwriting existing documents.
     *
     * Fetches previous period data to populate intelligent defaults:
     * - Cumulative metrics (total patients) carry over from previous period
     * - Change values are calculated based on previous period
     * - Motivational targets are set based on previous achievements
     */
    protected function createDefaultDocument(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        try {
            $documentId = $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp);

            // Fetch previous period data for intelligent defaults
            $previousData = $this->fetchPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);

            // Create document with previous period data for comparison
            $document = $this->getDefaultDocumentWithPreviousData($periodType, $tenantId, $workspaceId, $timestamp, $previousData);

            $response = $this->client->index([
                'index' => $this->getIndexName($periodType),
                'id' => $documentId,
                'body' => $document,
                'op_type' => 'create' // Only create if doesn't exist
            ]);

            Log::channel('elasticsearch')->info('Created default dashboard metrics document', [
                'index' => $this->getIndexName($periodType),
                'document_id' => $documentId,
                'tenant_id' => $tenantId,
                'workspace_id' => $workspaceId,
                'period_type' => $periodType,
                'has_previous_data' => !empty($previousData)
            ]);

            return ['success' => true, 'id' => $response['_id'] ?? null];

        } catch (\Throwable $e) {
            // Document already exists (conflict) - that's fine
            if (str_contains($e->getMessage(), 'version_conflict_engine_exception') ||
                str_contains($e->getMessage(), 'document already exists')) {
                return ['success' => true, 'already_exists' => true];
            }

            Log::channel('elasticsearch')->warning('Failed to create default dashboard document', [
                'error' => $e->getMessage(),
                'period_type' => $periodType,
                'tenant_id' => $tenantId
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fetch previous period data for comparison.
     */
    protected function fetchPreviousPeriodData(string $periodType, int $tenantId, mixed $workspaceId, Carbon $currentTimestamp): ?array
    {
        try {
            $previousTimestamp = match ($periodType) {
                self::PERIOD_DAILY => $currentTimestamp->copy()->subDay(),
                self::PERIOD_WEEKLY => $currentTimestamp->copy()->subWeek(),
                self::PERIOD_MONTHLY => $currentTimestamp->copy()->subMonth(),
                self::PERIOD_YEARLY => $currentTimestamp->copy()->subYear(),
                default => $currentTimestamp->copy()->subDay()
            };

            return $this->getDocument($periodType, $tenantId, $workspaceId, $previousTimestamp);
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->debug('Could not fetch previous period data', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);
            return null;
        }
    }

    /**
     * Create default document with intelligent defaults based on previous period data.
     *
     * - Cumulative metrics (patients): carry over previous count
     * - Incremental metrics (new-patients, revenue, treatment): set to 0, change based on previous
     * - Motivational stats: set targets based on previous achievements
     */
    protected function getDefaultDocumentWithPreviousData(
        string $periodType,
        int $tenantId,
        mixed $workspaceId,
        Carbon $timestamp,
        ?array $previousData
    ): array {
        $document = $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $timestamp);

        if (empty($previousData)) {
            return $document;
        }

        // Update statistics with previous period data
        $document['statistics'] = $this->getStatisticsWithPreviousData(
            $document['statistics'],
            $previousData['statistics'] ?? [],
            $periodType
        );

        // Update motivational stats with targets based on previous data
        $document['motivational_stats'] = $this->getMotivationalStatsWithPreviousData(
            $document['motivational_stats'],
            $previousData['motivational_stats'] ?? [],
            $previousData['statistics'] ?? []
        );

        // Update cashier with previous period comparison
        $document['cashier'] = $this->getCashierWithPreviousData(
            $document['cashier'],
            $previousData['cashier'] ?? [],
            $periodType
        );

        // Update billing with previous period comparison
        $document['billing'] = $this->getBillingWithPreviousData(
            $document['billing'],
            $previousData['billing'] ?? [],
            $periodType
        );

        // Update pending items with previous period comparison
        $document['pending_items'] = $this->getPendingItemsWithPreviousData(
            $document['pending_items'],
            $previousData['pending_items'] ?? [],
            $periodType
        );

        return $document;
    }

    /**
     * Populate statistics with previous_count from previous period.
     *
     * - patients (cumulative): count = previous count, previous_count = previous count
     * - others (incremental): count = 0, previous_count = previous count
     *
     * The transformer will calculate change, change_type, percentage_change.
     */
    protected function getStatisticsWithPreviousData(array $currentStats, array $previousStats, string $periodType): array
    {
        $previousById = $this->indexArrayById($previousStats);

        foreach ($currentStats as &$stat) {
            $id = $stat['id'] ?? '';
            $previousStat = $previousById[$id] ?? null;

            if (!$previousStat) {
                continue;
            }

            $previousCount = (float) ($previousStat['count'] ?? 0);
            $stat['previous_count'] = $previousCount;

            // For cumulative metrics (patients), carry over the count
            if ($stat['is_cumulative'] ?? false) {
                $stat['count'] = $previousCount;
            }
            // For incremental metrics, count stays at 0

            $stat['updated_at'] = now()->toIso8601String();
        }

        return $currentStats;
    }

    /**
     * Populate motivational stats with previous period data.
     *
     * The transformer will calculate percentage, remaining, growth, growth_percentage.
     */
    protected function getMotivationalStatsWithPreviousData(array $currentStats, array $previousStats, array $previousStatistics): array
    {
        $previousCurrent = (float) ($previousStats['current'] ?? 0);
        $previousTarget = (float) ($previousStats['target'] ?? 0);

        // Store previous values for comparison
        $currentStats['previous_current'] = $previousCurrent;
        $currentStats['previous_target'] = $previousTarget;

        // Set target based on previous achievement + 10% growth
        if ($previousCurrent > 0) {
            $currentStats['target'] = (int) ceil($previousCurrent * 1.1);
        } elseif ($previousTarget > 0) {
            $currentStats['target'] = (int) $previousTarget;
        } else {
            // Default target based on previous new patients
            $previousPatients = $this->findStatisticValueById($previousStatistics, 'new-patients');
            $currentStats['target'] = $previousPatients > 0 ? (int) ceil($previousPatients * 1.1) : 10;
        }

        // Current period starts at 0
        $currentStats['current'] = 0;

        return $currentStats;
    }

    /**
     * Populate cashier with previous_count from previous period.
     *
     * The transformer will calculate change, change_type, percentage_change.
     */
    protected function getCashierWithPreviousData(array $currentCashier, array $previousCashier, string $periodType): array
    {
        $previousById = $this->indexArrayById($previousCashier);

        foreach ($currentCashier as &$item) {
            $id = $item['id'] ?? '';
            $previousItem = $previousById[$id] ?? null;

            if ($previousItem) {
                $item['previous_count'] = (float) ($previousItem['count'] ?? 0);
            }
            // count stays at 0, transformer calculates change

            $item['updated_at'] = now()->toIso8601String();
        }

        return $currentCashier;
    }

    /**
     * Populate billing with previous_count from previous period.
     *
     * The transformer will calculate change, change_type, percentage_change.
     */
    protected function getBillingWithPreviousData(array $currentBilling, array $previousBilling, string $periodType): array
    {
        $previousById = $this->indexArrayById($previousBilling);

        foreach ($currentBilling as &$item) {
            $id = $item['id'] ?? '';
            $previousItem = $previousById[$id] ?? null;

            if ($previousItem) {
                $item['previous_count'] = (float) ($previousItem['count'] ?? 0);
            }
            // count stays at 0, transformer calculates change

            $item['updated_at'] = now()->toIso8601String();
        }

        return $currentBilling;
    }

    /**
     * Populate pending items with previous_count from previous period.
     *
     * The transformer will calculate change if needed.
     */
    protected function getPendingItemsWithPreviousData(array $currentItems, array $previousItems, string $periodType): array
    {
        $previousById = $this->indexArrayById($previousItems);

        foreach ($currentItems as &$item) {
            $id = $item['id'] ?? '';
            $previousItem = $previousById[$id] ?? null;

            if ($previousItem) {
                $item['previous_count'] = (int) ($previousItem['count'] ?? 0);
            }
            // count stays at 0

            $item['updated_at'] = now()->toIso8601String();
        }

        return $currentItems;
    }

    /**
     * Index array by 'id' field for quick lookup.
     */
    protected function indexArrayById(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $indexed[$item['id']] = $item;
            }
        }
        return $indexed;
    }

    /**
     * Find statistic value by ID.
     */
    protected function findStatisticValueById(array $statistics, string $id): float
    {
        foreach ($statistics as $stat) {
            if (($stat['id'] ?? '') === $id) {
                return (float) ($stat['count'] ?? 0);
            }
        }
        return 0;
    }

    /**
     * Store/update document in Elasticsearch.
     * This method will create or update the document (upsert behavior).
     */
    protected function storeDocument(array $data, string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
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
            Log::channel('elasticsearch')->error('Failed to store document', [
                'error' => $e->getMessage(),
                'period_type' => $periodType,
                'tenant_id' => $tenantId
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get document from Elasticsearch by ID.
     */
    protected function getDocument(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): ?array
    {
        try {
            $response = $this->client->get([
                'index' => $this->getIndexName($periodType),
                'id' => $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp)
            ]);

            return $response['_source'] ?? null;

        } catch (\Throwable $e) {
            return null;
        }
    }
}
