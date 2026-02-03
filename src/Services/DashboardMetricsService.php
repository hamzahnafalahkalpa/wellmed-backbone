<?php

namespace Projects\WellmedBackbone\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Projects\WellmedBackbone\Services\Concerns\HasPendingItem;
use Projects\WellmedBackbone\Services\Concerns\HasStatistic;

/**
 * Dashboard Metrics Service
 *
 * Handles storage and retrieval of dashboard metrics data in Elasticsearch
 * Supports daily, weekly, monthly, and yearly aggregations.
 */
class DashboardMetricsService
{
    use HasStatistic, HasPendingItem;

    protected $client;
    protected string $indexPrefix = 'dashboard-metrics';

    /**
     * Period types supported
     */
    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_YEARLY = 'yearly';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    public function getPeriodTypes():array{
        return [self::PERIOD_DAILY, self::PERIOD_WEEKLY, self::PERIOD_MONTHLY, self::PERIOD_YEARLY];
    }

    /**
     * Get or create current period document.
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function getOrCreateCurrentPeriod(string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        $documentId = $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp);
        try {
            $this->ensureIndexExists($periodType);

            $response = $this->client->get([
                'index' => $this->getIndexName($periodType),
                'id' => $documentId
            ]);
            return $response['_source'];

        } catch (\Throwable $e) {
            // Document doesn't exist, return default structure
            return $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $timestamp);
        }
    }

    /**
     * Get previous period data for comparison.
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $currentTimestamp
     * @return array
     */
    protected function getPreviousPeriodData(string $periodType, int $tenantId, mixed $workspaceId, Carbon $currentTimestamp): array
    {
        $previousTimestamp = $this->getPreviousPeriodTimestamp($periodType, $currentTimestamp);
        
        try {
            $documentId = $this->generateDocumentId($periodType, $tenantId, $workspaceId, $previousTimestamp);
            $response = $this->client->get([
                'index' => $this->getIndexName($periodType),
                'id' => $documentId
            ]);
            return $response['_source'];

        } catch (\Throwable $e) {
            // Previous period doesn't exist
            return $this->getDefaultDocument($periodType, $tenantId, $workspaceId, $previousTimestamp);
        }
    }

    /**
     * Get timestamp for previous period.
     *
     * @param string $periodType
     * @param Carbon $current
     * @return Carbon
     */
    protected function getPreviousPeriodTimestamp(string $periodType, Carbon $current): Carbon
    {
        return match ($periodType) {
            self::PERIOD_DAILY => $current->copy()->subDay(),
            self::PERIOD_WEEKLY => $current->copy()->subWeek(),
            self::PERIOD_MONTHLY => $current->copy()->subMonth(),
            self::PERIOD_YEARLY => $current->copy()->subYear(),
            default => $current->copy()->subDay()
        };
    }

    /**
     * Store current period document.
     *
     * @param array $data
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function storeCurrentPeriod(array $data, string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        try {
            $documentId = $this->generateDocumentId($periodType, $tenantId, $workspaceId, $timestamp);

            $this->ensureIndexExists($periodType);

            $response = $this->client->index([
                'index' => $this->getIndexName($periodType),
                'id' => $documentId,
                'body' => $data
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'index' => $response['_index'] ?? null
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store current period', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate document ID for a period.
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return string
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
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
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
                'current' => 0,
                'target' => 0,
                'percentage' => 0,
                'remaining' => 0,
                'growth' => 0,
                'growth_percentage' => 0
            ],
            'pending_items' => $this->getDefaultPendingItems($periodType),
            // 'pending_items' => [
            //     [
            //         'id' => 'unsigned_visits',
            //         'label' => null, 
            //         'count' => 0,
            //         'icon' => 'mdi:file-document-edit-outline', 
            //         'color' => 'text-orange-600', 
            //         'link' => '/patient-emr/unsigned-visits'
            //     ],
            //     [
            //         'id' => 'unsynced_patients',
            //         'label' => null, 
            //         'count' => 0,
            //         'icon' => 'mdi:sync-alert', 
            //         'color' => 'text-red-600', 
            //         'link' => '/satu-sehat/dashboard'
            //     ],
            //     [
            //         'id' => 'incomplete_diagnosis',
            //         'label' => null, 
            //         'count' => 0,
            //         'icon' => 'mdi:alert-circle', 
            //         'color' => 'text-amber-600', 
            //         'link' => '/patient-emr/incomplete-diagnosis'
            //     ]
            // ],
            'queue_services' => [],
            'diagnosis_treatment' => [],
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

    /**
     * Get change label based on period type.
     *
     * @param string $periodType
     * @return string
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
     * Ensure index exists, create if not.
     *
     * @param string $periodType
     * @return bool
     */
    protected function ensureIndexExists(string $periodType): bool
    {
        try {
            $indexName = $this->getIndexName($periodType);
            $exists = $this->client->indices()->exists(['index' => $indexName]);
            if (!$exists) {
                $this->client->indices()->create([
                    'index' => $indexName,
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 1,
                            'number_of_replicas' => 0
                        ],
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
                                'pending_items' => ['type' => 'object'],
                                'queue_services' => ['type' => 'nested'],
                                'diagnosis_treatment' => ['type' => 'nested'],
                                'aggregation_period' => ['type' => 'object'],
                                'metadata' => ['type' => 'object']
                            ]
                        ]
                    ]
                ]);

                Log::channel('elasticsearch')->info('Created dashboard metrics index', [
                    'index' => $indexName
                ]);
            }

            return true;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to ensure index exists', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);

            return true; // Continue anyway, ES might auto-create
        }
    }

    /**
     * Store dashboard metrics data
     *
     * @param array $data Dashboard metrics data
     * @param string $periodType One of: 'daily', 'monthly', 'yearly'
     * @param int|null $tenantId Tenant ID
     * @param mixed $workspaceId Workspace ID
     * @return array Response from Elasticsearch
     */
    public function store(array $data, string $periodType = 'daily', ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $document = $this->prepareDocument($data, $periodType, $tenantId, $workspaceId, $timestamp);

            $params = [
                'index' => $this->getIndexName($periodType),
                'body' => $document,
            ];

            $response = $this->client->index($params);

            Log::channel('elasticsearch')->info('Dashboard metrics stored', [
                'tenant_id' => $tenantId,
                'period_type' => $periodType,
                'document_id' => $response['_id'] ?? null
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'index' => $response['_index'] ?? null,
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store dashboard metrics', [
                'error' => $e->getMessage(),
                'period_type' => $periodType,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve dashboard metrics
     *
     * @param string $periodType One of: 'daily', 'monthly', 'yearly'
     * @param array $filters Additional filters (date, tenant_id, workspace_id)
     * @return array Dashboard metrics data
     */
    public function get(string $periodType = 'daily', array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = $this->buildQuery($periodType, $tenantId, $workspaceId, $filters);

            $params = [
                'index' => $this->getIndexName($periodType),
                'body' => $query,
                'size' => $filters['size'] ?? 1,
                'sort' => ['timestamp:desc']
            ];

            $response = $this->client->search($params);

            if (empty($response['hits']['hits'])) {
                return [
                    'success' => true,
                    'data' => null,
                    'message' => 'No data found'
                ];
            }

            $data = $response['hits']['hits'][0]['_source'];

            return [
                'success' => true,
                'data' => $data,
                'total' => $response['hits']['total']['value'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to retrieve dashboard metrics', [
                'error' => $e->getMessage(),
                'period_type' => $periodType,
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get metrics for a date range
     *
     * @param string $periodType
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $additionalFilters
     * @return array
     */
    public function getRange(string $periodType, Carbon $startDate, Carbon $endDate, array $additionalFilters = []): array
    {
        try {
            $tenantId = $additionalFilters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $additionalFilters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['tenant_id' => $tenantId]],
                            ['term' => ['period_type' => $periodType]],
                            [
                                'range' => [
                                    'timestamp' => [
                                        'gte' => $startDate->toIso8601String(),
                                        'lte' => $endDate->toIso8601String()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    ['timestamp' => ['order' => 'desc']]
                ]
            ];

            if ($workspaceId) {
                $query['query']['bool']['must'][] = ['term' => ['workspace_id' => $workspaceId]];
            }

            $params = [
                'index' => $this->getIndexName($periodType),
                'body' => $query,
                'size' => $additionalFilters['size'] ?? 100
            ];

            $response = $this->client->search($params);

            $results = [];
            foreach ($response['hits']['hits'] as $hit) {
                $results[] = $hit['_source'];
            }

            return [
                'success' => true,
                'data' => $results,
                'total' => $response['hits']['total']['value'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to retrieve dashboard metrics range', [
                'error' => $e->getMessage(),
                'period_type' => $periodType,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Aggregate statistics for a period
     *
     * @param string $periodType
     * @param string $metric Metric to aggregate (patients, revenue, etc.)
     * @param string $aggregation Aggregation type (sum, avg, min, max)
     * @param array $filters
     * @return array
     */
    public function aggregate(string $periodType, string $metric, string $aggregation = 'sum', array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = $this->buildQuery($periodType, $tenantId, $workspaceId, $filters);

            $query['aggs'] = [
                'metric_aggregation' => [
                    $aggregation => [
                        'field' => "statistics.{$metric}.count"
                    ]
                ]
            ];

            $params = [
                'index' => $this->getIndexName($periodType),
                'body' => $query,
                'size' => 0 // We only need aggregations
            ];

            $response = $this->client->search($params);

            return [
                'success' => true,
                'value' => $response['aggregations']['metric_aggregation']['value'] ?? 0,
                'total_documents' => $response['hits']['total']['value'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to aggregate dashboard metrics', [
                'error' => $e->getMessage(),
                'metric' => $metric,
                'aggregation' => $aggregation
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare document for Elasticsearch indexing
     *
     * @param array $data
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function prepareDocument(array $data, string $periodType, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        return [
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'period_type' => $periodType,
            'timestamp' => $timestamp->toIso8601String(),
            'date' => $timestamp->toDateString(),
            'year' => $timestamp->year,
            'month' => $timestamp->month,
            'day' => $timestamp->day,
            'statistics' => [
                [
                    "id" => "patients",
                    "label" => "Jumlah Pasien",
                    "count" => $data['statistics']['patients']['count'] ?? 0,
                    "change" => $data['statistics']['patients']['change'] ?? 0,
                    "change_type" => $data['statistics']['patients']['change_type'] ?? 'increase',
                    "percentage_change" => $data['statistics']['patients']['percentage_change'] ?? 0.0,
                    "change_label" => "Dari kemarin",
                    "icon" => "mdi:account-group",
                    "color" => "blue",
                    "gradient" => "from-blue-500 to-cyan-400",
                    "bg_light" => "bg-blue-50",
                    "text_color" => "text-blue-600",
                    "border_color" => "border-blue-200"
                ],
                [
                    "id" => "new-patients",
                    "label" => "Pasien Baru",
                    "count" => $data['statistics']['new_patients']['count'] ?? 0,
                    "change" => $data['statistics']['new_patients']['change'] ?? 0,
                    "change_type" => $data['statistics']['new_patients']['change_type'] ?? 'increase',
                    "percentage_change" => $data['statistics']['new_patients']['percentage_change'] ?? 0.0,
                    "change_label" => "Dari kemarin",
                    "icon" => "mdi:account-plus",
                    "color" => "purple",
                    "gradient" => "from-purple-500 to-pink-400",
                    "bg_light" => "bg-purple-50",
                    "text_color" => "text-purple-600",
                    "border_color" => "border-purple-200"
                ],
                [
                    "id" => "revenue",
                    "label" => "Omzet",
                    "count" => $data['statistics']['revenue']['count'] ?? 0,
                    "change" => $data['statistics']['revenue']['change'] ?? 0,
                    "change_type" => $data['statistics']['revenue']['change_type'] ?? 'increase',
                    "percentage_change" => $data['statistics']['revenue']['percentage_change'] ?? 0.0,
                    "change_label" => "Dari kemarin",
                    "icon" => "mdi:cash-multiple",
                    "color" => "emerald",
                    "gradient" => "from-emerald-500 to-teal-400",
                    "bg_light" => "bg-emerald-50",
                    "text_color" => "text-emerald-600",
                    "border_color" => "border-emerald-200",
                    "is_currency" => true
                ],
                [
                    "id" => "treatment",
                    "label" => "Tindakan Dipesankan",
                    "count" => $data['statistics']['treatment']['count'] ?? 0,
                    "change" => $data['statistics']['treatment']['change'] ?? 0,
                    "change_type" => $data['statistics']['treatment']['change_type'] ?? 'increase',
                    "percentage_change" => $data['statistics']['treatment']['percentage_change'] ?? 0.0,
                    "change_label" => "Dari kemarin",
                    "icon" => "mdi:clipboard-list",
                    "color" => "orange",
                    "gradient" => "from-orange-500 to-amber-400",
                    "bg_light" => "bg-orange-50",
                    "text_color" => "text-orange-600",
                    "border_color" => "border-orange-200"
                ]
            ],
            'motivational_stats' => [
                'today' => $data['motivational_stats']['today'] ?? 0,
                'yesterday' => $data['motivational_stats']['yesterday'] ?? 0,
                'target' => $data['motivational_stats']['target'] ?? 0,
                'percentage' => $data['motivational_stats']['percentage'] ?? 0.0
            ],
            'pending_items' => [
                [
                    "id" => 'unsigned_visits',
                    "label" => ($data['pending_items']['unsigned_visits'] ?? 0).' unsigned visits',
                    "count" => ($data['pending_items']['unsigned_visits'] ?? 0),
                    "icon" => "mdi:file-document-edit-outline",
                    "color" => "text-orange-600",
                    "link" => '/patient-emr/unsigned-visits'
                ],
                [
                    "id" => 'unsynced_patients',
                    "label" => ($data['pending_items']['unsynced_patients'] ?? 0).' belum tersinkronisasi satu sehat',
                    "count" => ($data['pending_items']['unsynced_patients'] ?? 0),
                    "icon" => "mdi:sync-alert",
                    "color" => "text-red-600",
                    "link" => '/satu-sehat/dashboard'
                ],
                [
                    "id" => 'incomplete_diagnosis',
                    "label" => ($data['pending_items']['incomplete_diagnosis'] ?? 0)." tanpa ICD",
                    "count" => ($data['pending_items']['incomplete_diagnosis'] ?? 0),
                    "icon" => "mdi:alert-circle",
                    "color" => "text-amber-600",
                    "link" => "/patient-emr/incomplete-diagnosis",
                ]
            ],
            'queue_services' => $data['queue_services'] ?? [],
            'diagnosis_treatment' => $data['diagnosis_treatment'] ?? [],
            'aggregation_period' => [
                'start_date' => $data['aggregation_period']['start_date'] ?? $timestamp->toDateString(),
                'end_date' => $data['aggregation_period']['end_date'] ?? $timestamp->toDateString(),
                'label' => $data['aggregation_period']['label'] ?? $this->getPeriodLabel($periodType, $timestamp)
            ],
            'metadata' => [
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
                'created_by' => $data['metadata']['created_by'] ?? 'system',
                'version' => $data['metadata']['version'] ?? '1.0'
            ]
        ];
    }

    /**
     * Build Elasticsearch query
     *
     * @param string $periodType
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param array $filters
     * @return array
     */
    protected function buildQuery(string $periodType, int $tenantId, mixed $workspaceId, array $filters): array
    {
        $must = [
            ['term' => ['tenant_id' => $tenantId]],
            ['term' => ['period_type' => $periodType]]
        ];

        if ($workspaceId) {
            $must[] = ['term' => ['workspace_id' => $workspaceId]];
        }

        // Date filter
        if (isset($filters['date'])) {
            $must[] = ['term' => ['date' => $filters['date']]];
        }

        // Year filter
        if (isset($filters['year'])) {
            $must[] = ['term' => ['year' => $filters['year']]];
        }

        // Month filter
        if (isset($filters['month'])) {
            $must[] = ['term' => ['month' => $filters['month']]];
        }

        // Day filter
        if (isset($filters['day'])) {
            $must[] = ['term' => ['day' => $filters['day']]];
        }

        return [
            'query' => [
                'bool' => [
                    'must' => $must
                ]
            ]
        ];
    }

    /**
     * Get index name based on period type
     *
     * @param string $periodType
     * @return string
     */
    protected function getIndexName(string $periodType): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');

        return $prefix . $separator . $this->indexPrefix . '-' . $periodType;
    }

    /**
     * Get period label
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return string
     */
    protected function getPeriodLabel(string $periodType, Carbon $timestamp): string
    {
        return match($periodType) {
            'daily' => $timestamp->format('d M Y'),
            'monthly' => $timestamp->format('F Y'),
            'yearly' => $timestamp->format('Y'),
            default => $timestamp->toDateString()
        };
    }

    /**
     * Get example dashboard data from JSON file.
     *
     * @param string|null $periodType If null, returns all period types
     * @return array
     */
    public function getExampleData(?string $periodType = null): array
    {
        $jsonPath = __DIR__ . '/../Data/dashboard-metrics-example.json';

        if (!file_exists($jsonPath)) {
            return [
                'success' => false,
                'error' => 'Example data file not found'
            ];
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'Failed to parse example data: ' . json_last_error_msg()
            ];
        }

        if ($periodType && isset($data[$periodType])) {
            return [
                'success' => true,
                'data' => $data[$periodType],
                'is_example' => true
            ];
        }

        return [
            'success' => true,
            'data' => $data,
            'is_example' => true
        ];
    }

    /**
     * Delete metrics by filters
     *
     * @param string $periodType
     * @param array $filters
     * @return array
     */
    public function delete(string $periodType, array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = $this->buildQuery($periodType, $tenantId, $workspaceId, $filters);

            $params = [
                'index' => $this->getIndexName($periodType),
                'body' => $query
            ];

            $response = $this->client->deleteByQuery($params);

            return [
                'success' => true,
                'deleted' => $response['deleted'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to delete dashboard metrics', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
