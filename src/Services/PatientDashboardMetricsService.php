<?php

namespace Projects\WellmedBackbone\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Projects\WellmedBackbone\Services\Concerns\HasPatientIntegration;

/**
 * Patient Dashboard Metrics Service
 *
 * Handles storage and retrieval of patient-level dashboard metrics data in Elasticsearch.
 * Only supports daily aggregations with patient_id as prefix in the index.
 */
class PatientDashboardMetricsService
{
    use HasPatientIntegration;

    protected $client;
    protected string $indexPrefix = 'patient-dashboard-metrics';

    /**
     * Period types supported (only daily for patient-level metrics)
     */
    public const PERIOD_DAILY = 'daily';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    /**
     * Get supported period types (only daily).
     *
     * @return array
     */
    public function getPeriodTypes(): array
    {
        return [self::PERIOD_DAILY];
    }

    /**
     * Get or create current period document for a patient.
     *
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function getOrCreateCurrentPeriod(string $patientId, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        $documentId = $this->generateDocumentId($patientId, $tenantId, $workspaceId, $timestamp);
        $defaultDocument = $this->getDefaultDocument($patientId, $tenantId, $workspaceId, $timestamp);

        try {
            $this->ensureIndexExists($patientId);

            $response = $this->client->get([
                'index' => $this->getIndexName($patientId),
                'id' => $documentId
            ]);

            $existingData = $response['_source'];

            return $this->mergeWithDefaults($existingData, $defaultDocument);

        } catch (\Throwable $e) {
            return $defaultDocument;
        }
    }

    /**
     * Merge existing document data with default structure.
     *
     * @param array $existing
     * @param array $defaults
     * @return array
     */
    protected function mergeWithDefaults(array $existing, array $defaults): array
    {
        $merged = $defaults;

        // Preserve existing scalar values
        foreach (['patient_id', 'tenant_id', 'workspace_id', 'period_type', 'timestamp', 'date', 'year', 'month', 'week', 'day'] as $key) {
            if (isset($existing[$key])) {
                $merged[$key] = $existing[$key];
            }
        }

        // Merge patient_integrations - preserve existing sync status
        if (isset($existing['patient_integrations']) && is_array($existing['patient_integrations'])) {
            $merged['patient_integrations'] = $this->mergeArrayByType($defaults['patient_integrations'] ?? [], $existing['patient_integrations']);
        }

        // Merge visit_history
        if (isset($existing['visit_history']) && is_array($existing['visit_history'])) {
            $merged['visit_history'] = $existing['visit_history'];
        }

        // Merge encounter_summary
        if (isset($existing['encounter_summary'])) {
            $merged['encounter_summary'] = array_merge($defaults['encounter_summary'], $existing['encounter_summary']);
        }

        if (isset($existing['aggregation_period'])) {
            $merged['aggregation_period'] = array_merge($defaults['aggregation_period'], $existing['aggregation_period']);
        }

        if (isset($existing['metadata'])) {
            $merged['metadata'] = array_merge($defaults['metadata'], $existing['metadata']);
        }

        return $merged;
    }

    /**
     * Merge arrays by type field.
     *
     * @param array $defaults
     * @param array $existing
     * @return array
     */
    protected function mergeArrayByType(array $defaults, array $existing): array
    {
        $merged = [];
        $existingByType = [];

        foreach ($existing as $item) {
            if (isset($item['type'])) {
                $existingByType[$item['type']] = $item;
            }
        }

        foreach ($defaults as $defaultItem) {
            $type = $defaultItem['type'] ?? null;
            if ($type && isset($existingByType[$type])) {
                $mergedItem = $defaultItem;
                foreach ($existingByType[$type] as $key => $value) {
                    $mergedItem[$key] = $value;
                }
                $merged[] = $mergedItem;
                unset($existingByType[$type]);
            } else {
                $merged[] = $defaultItem;
            }
        }

        foreach ($existingByType as $item) {
            $merged[] = $item;
        }

        return $merged;
    }

    /**
     * Get previous period data for comparison.
     *
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $currentTimestamp
     * @return array
     */
    protected function getPreviousPeriodData(string $patientId, int $tenantId, mixed $workspaceId, Carbon $currentTimestamp): array
    {
        $previousTimestamp = $currentTimestamp->copy()->subDay();

        try {
            $documentId = $this->generateDocumentId($patientId, $tenantId, $workspaceId, $previousTimestamp);
            $response = $this->client->get([
                'index' => $this->getIndexName($patientId),
                'id' => $documentId
            ]);
            return $response['_source'];

        } catch (\Throwable $e) {
            return $this->getDefaultDocument($patientId, $tenantId, $workspaceId, $previousTimestamp);
        }
    }

    /**
     * Store current period document.
     *
     * @param array $data
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function storeCurrentPeriod(array $data, string $patientId, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        try {
            $documentId = $this->generateDocumentId($patientId, $tenantId, $workspaceId, $timestamp);

            $this->ensureIndexExists($patientId);
            $response = $this->client->index([
                'index' => $this->getIndexName($patientId),
                'id' => $documentId,
                'body' => $data
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'index' => $response['_index'] ?? null
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store patient current period', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate document ID for a patient period.
     *
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return string
     */
    protected function generateDocumentId(string $patientId, int $tenantId, mixed $workspaceId, Carbon $timestamp): string
    {
        $periodKey = $timestamp->format('Y-m-d');
        return "daily_{$patientId}_{$tenantId}_{$workspaceId}_{$periodKey}";
    }

    /**
     * Get default document structure for patient.
     *
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function getDefaultDocument(string $patientId, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        return [
            'patient_id' => $patientId,
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'period_type' => self::PERIOD_DAILY,
            'timestamp' => $timestamp->toIso8601String(),
            'date' => $timestamp->toDateString(),
            'year' => $timestamp->year,
            'month' => $timestamp->month,
            'week' => (int) $timestamp->format('W'),
            'day' => $timestamp->day,
            'patient_integrations' => $this->getDefaultPatientIntegrations(),
            'visit_history' => [],
            'encounter_summary' => [
                'total_encounters' => 0,
                'last_encounter_date' => null,
                'pending_encounters' => 0
            ],
            'aggregation_period' => [
                'start_date' => $timestamp->toDateString(),
                'end_date' => $timestamp->toDateString(),
                'label' => $timestamp->format('d M Y')
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
     * Get change label (always yesterday for daily).
     *
     * @return string
     */
    protected function getChangeLabel(): string
    {
        return 'Dari kemarin';
    }

    /**
     * Ensure index exists for a patient.
     *
     * @param string $patientId
     * @return bool
     */
    protected function ensureIndexExists(string $patientId): bool
    {
        try {
            $indexName = $this->getIndexName($patientId);
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
                                'patient_id' => ['type' => 'integer'],
                                'tenant_id' => ['type' => 'integer'],
                                'workspace_id' => ['type' => 'integer'],
                                'period_type' => ['type' => 'keyword'],
                                'timestamp' => ['type' => 'date'],
                                'date' => ['type' => 'date', 'format' => 'yyyy-MM-dd'],
                                'year' => ['type' => 'integer'],
                                'month' => ['type' => 'integer'],
                                'week' => ['type' => 'integer'],
                                'day' => ['type' => 'integer'],
                                'patient_integrations' => ['type' => 'nested'],
                                'visit_history' => ['type' => 'nested'],
                                'encounter_summary' => ['type' => 'object'],
                                'aggregation_period' => ['type' => 'object'],
                                'metadata' => ['type' => 'object']
                            ]
                        ]
                    ]
                ]);

                Log::channel('elasticsearch')->info('Created patient dashboard metrics index', [
                    'index' => $indexName,
                    'patient_id' => $patientId
                ]);
            }

            return true;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to ensure patient index exists', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId
            ]);

            return true;
        }
    }

    /**
     * Store patient dashboard metrics data.
     *
     * @param array $data Dashboard metrics data
     * @param string $patientId Patient ID
     * @param int|null $tenantId Tenant ID
     * @param mixed $workspaceId Workspace ID
     * @return array Response from Elasticsearch
     */
    public function store(array $data, string $patientId, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $document = $this->prepareDocument($data, $patientId, $tenantId, $workspaceId, $timestamp);

            $params = [
                'index' => $this->getIndexName($patientId),
                'body' => $document,
            ];

            $response = $this->client->index($params);

            Log::channel('elasticsearch')->info('Patient dashboard metrics stored', [
                'patient_id' => $patientId,
                'tenant_id' => $tenantId,
                'document_id' => $response['_id'] ?? null
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'index' => $response['_index'] ?? null,
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store patient dashboard metrics', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve patient dashboard metrics.
     *
     * @param string $patientId Patient ID
     * @param array $filters Additional filters (date, tenant_id, workspace_id)
     * @return array Dashboard metrics data
     */
    public function get(string $patientId, array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = $this->buildQuery($patientId, $tenantId, $workspaceId, $filters);

            $params = [
                'index' => $this->getIndexName($patientId),
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
            Log::channel('elasticsearch')->error('Failed to retrieve patient dashboard metrics', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get metrics for a date range for a patient.
     *
     * @param string $patientId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $additionalFilters
     * @return array
     */
    public function getRange(string $patientId, Carbon $startDate, Carbon $endDate, array $additionalFilters = []): array
    {
        try {
            $tenantId = $additionalFilters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $additionalFilters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['patient_id' => $patientId]],
                            ['term' => ['tenant_id' => $tenantId]],
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
                'index' => $this->getIndexName($patientId),
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
            Log::channel('elasticsearch')->error('Failed to retrieve patient dashboard metrics range', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
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
     * Prepare document for Elasticsearch indexing.
     *
     * @param array $data
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param Carbon $timestamp
     * @return array
     */
    protected function prepareDocument(array $data, string $patientId, int $tenantId, mixed $workspaceId, Carbon $timestamp): array
    {
        return [
            'patient_id' => $patientId,
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'period_type' => self::PERIOD_DAILY,
            'timestamp' => $timestamp->toIso8601String(),
            'date' => $timestamp->toDateString(),
            'year' => $timestamp->year,
            'month' => $timestamp->month,
            'day' => $timestamp->day,
            'patient_integrations' => $data['patient_integrations'] ?? $this->getDefaultPatientIntegrations(),
            'visit_history' => $data['visit_history'] ?? [],
            'encounter_summary' => [
                'total_encounters' => $data['encounter_summary']['total_encounters'] ?? 0,
                'last_encounter_date' => $data['encounter_summary']['last_encounter_date'] ?? null,
                'pending_encounters' => $data['encounter_summary']['pending_encounters'] ?? 0
            ],
            'aggregation_period' => [
                'start_date' => $data['aggregation_period']['start_date'] ?? $timestamp->toDateString(),
                'end_date' => $data['aggregation_period']['end_date'] ?? $timestamp->toDateString(),
                'label' => $data['aggregation_period']['label'] ?? $timestamp->format('d M Y')
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
     * Build Elasticsearch query for patient.
     *
     * @param string $patientId
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param array $filters
     * @return array
     */
    protected function buildQuery(string $patientId, int $tenantId, mixed $workspaceId, array $filters): array
    {
        $must = [
            ['term' => ['patient_id' => $patientId]],
            ['term' => ['tenant_id' => $tenantId]]
        ];

        if ($workspaceId) {
            $must[] = ['term' => ['workspace_id' => $workspaceId]];
        }

        if (isset($filters['date'])) {
            $must[] = ['term' => ['date' => $filters['date']]];
        }

        if (isset($filters['year'])) {
            $must[] = ['term' => ['year' => $filters['year']]];
        }

        if (isset($filters['month'])) {
            $must[] = ['term' => ['month' => $filters['month']]];
        }

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
     * Get index name with patient_id prefix.
     *
     * @param string $patientId
     * @return string
     */
    protected function getIndexName(string $patientId): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');

        return $prefix . $separator . $this->indexPrefix . '-' . $patientId . '-daily';
    }

    /**
     * Delete patient metrics by filters.
     *
     * @param string $patientId
     * @param array $filters
     * @return array
     */
    public function delete(string $patientId, array $filters = []): array
    {
        try {
            $tenantId = $filters['tenant_id'] ?? tenancy()->tenant->getKey();
            $workspaceId = $filters['workspace_id'] ?? tenancy()->tenant->reference?->getKey();

            $query = $this->buildQuery($patientId, $tenantId, $workspaceId, $filters);

            $params = [
                'index' => $this->getIndexName($patientId),
                'body' => $query
            ];

            $response = $this->client->deleteByQuery($params);

            return [
                'success' => true,
                'deleted' => $response['deleted'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to delete patient dashboard metrics', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
