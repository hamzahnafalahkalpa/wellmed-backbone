<?php

namespace Projects\WellmedBackbone\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Dashboard Metrics Service
 *
 * Handles storage and retrieval of dashboard metrics data in Elasticsearch
 * Supports daily, monthly, and yearly aggregations.
 */
class DashboardMetricsService
{
    protected $client;
    protected string $indexPrefix = 'dashboard-metrics';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    /**
     * Store dashboard metrics data
     *
     * @param array $data Dashboard metrics data
     * @param string $periodType One of: 'daily', 'monthly', 'yearly'
     * @param int|null $tenantId Tenant ID
     * @param int|null $workspaceId Workspace ID
     * @return array Response from Elasticsearch
     */
    public function store(array $data, string $periodType = 'daily', ?int $tenantId = null, ?int $workspaceId = null): array
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
     * @param int|null $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function prepareDocument(array $data, string $periodType, int $tenantId, ?int $workspaceId, Carbon $timestamp): array
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
                'patients' => [
                    'count' => $data['statistics']['patients']['count'] ?? 0,
                    'change' => $data['statistics']['patients']['change'] ?? 0,
                    'change_type' => $data['statistics']['patients']['change_type'] ?? 'increase',
                    'percentage_change' => $data['statistics']['patients']['percentage_change'] ?? 0.0
                ],
                'new_patients' => [
                    'count' => $data['statistics']['new_patients']['count'] ?? 0,
                    'change' => $data['statistics']['new_patients']['change'] ?? 0,
                    'change_type' => $data['statistics']['new_patients']['change_type'] ?? 'increase',
                    'percentage_change' => $data['statistics']['new_patients']['percentage_change'] ?? 0.0
                ],
                'revenue' => [
                    'count' => $data['statistics']['revenue']['count'] ?? 0,
                    'change' => $data['statistics']['revenue']['change'] ?? 0,
                    'change_type' => $data['statistics']['revenue']['change_type'] ?? 'increase',
                    'percentage_change' => $data['statistics']['revenue']['percentage_change'] ?? 0.0
                ],
                'unfinished' => [
                    'count' => $data['statistics']['unfinished']['count'] ?? 0,
                    'change' => $data['statistics']['unfinished']['change'] ?? 0,
                    'change_type' => $data['statistics']['unfinished']['change_type'] ?? 'increase',
                    'percentage_change' => $data['statistics']['unfinished']['percentage_change'] ?? 0.0
                ]
            ],
            'motivational_stats' => [
                'today' => $data['motivational_stats']['today'] ?? 0,
                'yesterday' => $data['motivational_stats']['yesterday'] ?? 0,
                'target' => $data['motivational_stats']['target'] ?? 0,
                'percentage' => $data['motivational_stats']['percentage'] ?? 0.0
            ],
            'pending_items' => [
                'unsigned_visits' => $data['pending_items']['unsigned_visits'] ?? 0,
                'unsynced_patients' => $data['pending_items']['unsynced_patients'] ?? 0,
                'incomplete_diagnosis' => $data['pending_items']['incomplete_diagnosis'] ?? 0
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
     * @param int|null $workspaceId
     * @param array $filters
     * @return array
     */
    protected function buildQuery(string $periodType, int $tenantId, ?int $workspaceId, array $filters): array
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
