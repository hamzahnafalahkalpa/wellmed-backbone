<?php

namespace Projects\WellmedBackbone\Schemas;

use Illuminate\Support\Facades\Log;

/**
 * Reporting Schema - Read/Query reporting data from Elasticsearch
 *
 * This class handles searching, filtering, and aggregating report data.
 */
class Reporting
{
    protected $client;

    // Report index types (must match backbone's ReportingService)
    public const INDEX_PATIENT = 'patient';
    public const INDEX_PATIENT_ILLNESS = 'patient_illness';
    public const INDEX_VISIT_PATIENT = 'visit_patient';
    public const INDEX_VISIT_REGISTRATION = 'visit_registration';
    public const INDEX_VISIT_EXAMINATION = 'visit_examination';
    public const INDEX_BILLING = 'billing';
    public const INDEX_INVOICE = 'invoice';
    public const INDEX_REFUND = 'refund';
    public const INDEX_WALLET_TRANSACTION = 'wallet_transaction';

    /**
     * Map index types to their corresponding model classes.
     * This allows us to read field types from model $casts.
     *
     * All models are in wellmed-backbone for centralized elastic_config management.
     */
    protected array $indexModelMap = [
        self::INDEX_PATIENT => \Projects\WellmedBackbone\Models\ModulePatient\Patient\Patient::class,
        self::INDEX_PATIENT_ILLNESS => \Projects\WellmedBackbone\Models\ModuleExamination\PatientIllness::class,
        self::INDEX_VISIT_PATIENT => \Projects\WellmedBackbone\Models\ModulePatient\EMR\VisitPatient::class,
        self::INDEX_VISIT_REGISTRATION => \Projects\WellmedBackbone\Models\ModulePatient\EMR\VisitRegistration::class,
        self::INDEX_VISIT_EXAMINATION => \Projects\WellmedBackbone\Models\ModulePatient\EMR\VisitExamination::class,
        self::INDEX_BILLING => \Projects\WellmedBackbone\Models\ModulePayment\Billing::class,
        self::INDEX_INVOICE => \Projects\WellmedBackbone\Models\ModulePayment\Invoice::class,
        self::INDEX_REFUND => \Projects\WellmedBackbone\Models\ModulePayment\Refund::class,
        self::INDEX_WALLET_TRANSACTION => \Projects\WellmedBackbone\Models\ModulePayment\WalletTransaction::class,
    ];

    /**
     * Cache for model instances and their casts
     */
    protected array $modelCastsCache = [];

    public function __construct()
    {
        if (config('elasticsearch.enabled', false)) {
            try {
                $this->client = app('elasticsearch');
            } catch (\Exception $e) {
                Log::warning('Elasticsearch client not available', ['error' => $e->getMessage()]);
                $this->client = null;
            }
        }
    }

    /**
     * Get index name with prefix.
     */
    public function getIndexName(string $indexType): string
    {
        $prefix = config('elasticsearch.prefix', config('app.env', 'development'));
        $separator = config('elasticsearch.separator', '.');
        $indexConfig = config("elasticsearch.indices.{$indexType}", ['name' => $indexType]);

        return $prefix . $separator . ($indexConfig['name'] ?? $indexType);
    }

    /**
     * Get model casts for a specific index type.
     * Reads from the model's $casts property.
     */
    protected function getModelCasts(string $indexType): array
    {
        // Return cached casts if available
        if (isset($this->modelCastsCache[$indexType])) {
            return $this->modelCastsCache[$indexType];
        }

        // Get model class for this index
        $modelClass = $this->indexModelMap[$indexType] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            $this->modelCastsCache[$indexType] = [];
            return [];
        }

        try {
            // Instantiate model and get casts
            $model = new $modelClass();
            $casts = $model->getCasts();

            $this->modelCastsCache[$indexType] = $casts;
            return $casts;
        } catch (\Exception $e) {
            Log::warning("Could not load model casts for index {$indexType}", [
                'model' => $modelClass,
                'error' => $e->getMessage()
            ]);
            $this->modelCastsCache[$indexType] = [];
            return [];
        }
    }

    /**
     * Search documents with pagination.
     */
    public function search(string $indexType, array $params = []): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $page = $params['page'] ?? 1;
            $perPage = $params['per_page'] ?? 10;
            $from = ($page - 1) * $perPage;

            $searchParams = [
                'index' => $this->getIndexName($indexType),
                'body' => [
                    'from' => $from,
                    'size' => $perPage,
                ],
            ];

            // Add query if filters provided
            if (!empty($params['filters'])) {
                $searchParams['body']['query'] = $this->buildQuery($params['filters'], $indexType);
            }

            // Add sorting
            if (!empty($params['sort'])) {
                $searchParams['body']['sort'] = $this->buildSort($params['sort']);
            }

            $response = $this->client->search($searchParams);
            $responseArray = $response->asArray();

            return $this->formatPaginatedResponse($responseArray, $page, $perPage);

        } catch (\Exception $e) {
            Log::error("Reporting search failed for {$indexType}", [
                'error' => $e->getMessage(),
                'params' => $params,
            ]);
            throw new \Exception("Failed to search {$indexType}: " . $e->getMessage());
        }
    }

    /**
     * Build query from filters.
     * Uses same pattern as HasElasticSearch trait for consistency.
     * Now reads field types from model $casts for accurate type detection.
     */
    protected function buildQuery(array $filters, string $indexType): array
    {
        $must = [];
        $modelCasts = $this->getModelCasts($indexType);

        foreach ($filters as $field => $value) {
            // Skip if empty (but allow 0 and false)
            if (is_null($value) || $value === '') {
                continue;
            }

            // Detect field type using model casts or fallback to heuristics
            $fieldType = $this->detectFieldType($field, $value, $modelCasts);
            $fieldQuery = $this->buildElasticFieldQuery($field, $value, $fieldType);

            if ($fieldQuery) {
                $must[] = $fieldQuery;
            }
        }

        // If no clauses, return match_all
        if (empty($must)) {
            return [
                'match_all' => new \stdClass()
            ];
        }

        return ['bool' => ['must' => $must]];
    }

    /**
     * Detect field type from field name and value.
     * Now uses model $casts for accurate type detection!
     * Similar to HasElasticSearch pattern but enhanced with model awareness.
     *
     * @param string $field Field name (can be nested like "patient.name")
     * @param mixed $value Field value
     * @param array $modelCasts Model's $casts array from getCasts()
     * @return string Field type for query building
     */
    protected function detectFieldType(string $field, mixed $value, array $modelCasts = []): string
    {
        // Extract base field name for nested fields (e.g., "patient.name" -> "name")
        $baseField = $field;
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $baseField = end($parts);
        }

        // PRIORITY 1: Check model casts first (most accurate)
        if (!empty($modelCasts)) {
            // Check exact field match
            if (isset($modelCasts[$field])) {
                return $this->normalizeCastType($modelCasts[$field]);
            }
            // Check base field match for nested fields
            if (isset($modelCasts[$baseField])) {
                return $this->normalizeCastType($modelCasts[$baseField]);
            }
        }

        // PRIORITY 2: Check value type
        if (is_array($value)) {
            return 'array';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        // PRIORITY 3: Heuristic-based detection (fallback)

        // Date fields
        if (str_ends_with($baseField, '_at') || str_ends_with($baseField, '_date') ||
            in_array($baseField, ['dob', 'visit_date', 'observed_at', 'paid_at', 'created_at', 'updated_at'])) {
            return 'date';
        }

        // Keyword fields (codes, IDs, medical_record, etc.) - exact or wildcard match only
        if (str_contains($baseField, 'code') || str_contains($baseField, '_id') ||
            in_array($baseField, ['medical_record', 'nik', 'invoice_code', 'billing_code', 'visit_code',
                                 'refund_code', 'status', 'payment_method', 'observation_type', 'type'])) {
            return 'keyword';
        }

        // Numeric fields
        if (in_array($baseField, ['id', 'age', 'total_amount', 'amount', 'count', 'total'])) {
            return 'integer';
        }

        // Boolean fields
        if (in_array($baseField, ['is_active', 'enabled', 'deleted'])) {
            return 'boolean';
        }

        // Numeric value detection
        if (is_numeric($value) && !str_contains($baseField, 'code') && !str_contains($baseField, 'phone')) {
            return 'integer';
        }

        // Default to string/text for LIKE search behavior
        return 'string';
    }

    /**
     * Normalize Laravel cast types to Elasticsearch field types.
     *
     * @param string $castType Laravel cast type (from $casts)
     * @return string Normalized type for ES query building
     */
    protected function normalizeCastType(string $castType): string
    {
        // Handle Laravel cast types
        return match(true) {
            // Date/Time types
            in_array($castType, ['date', 'datetime', 'timestamp', 'immutable_date', 'immutable_datetime']) => 'date',

            // Numeric types
            in_array($castType, ['integer', 'int', 'float', 'double', 'decimal', 'real']) => 'integer',

            // Boolean types
            in_array($castType, ['boolean', 'bool']) => 'boolean',

            // Array/JSON types
            in_array($castType, ['array', 'json', 'collection', 'object']) => 'array',

            // Encrypted strings are still strings
            $castType === 'encrypted' => 'string',

            // Custom cast classes - treat as string
            str_contains($castType, '\\') => 'string',

            // String is default
            default => 'string',
        };
    }

    /**
     * Build Elasticsearch query for a specific field.
     * Pattern from HasElasticSearch::buildElasticFieldQuery()
     */
    protected function buildElasticFieldQuery(string $field, mixed $value, string $fieldType): ?array
    {
        switch ($fieldType) {
            case 'keyword':
                // Keyword fields (codes, IDs) - use wildcard for partial match
                // Wildcards work on keyword fields, phrase_prefix does not
                return [
                    'wildcard' => [
                        $field => [
                            'value' => '*' . strtolower($value) . '*',
                            'case_insensitive' => true
                        ]
                    ]
                ];

            case 'string':
            case 'text':
                // LIKE behavior using multi_match with phrase_prefix
                // Only use base field, not .keyword (phrase_prefix doesn't work on keyword type)
                return [
                    'multi_match' => [
                        'query' => $value,
                        'fields' => [$field],
                        'type' => 'phrase_prefix'
                    ]
                ];

            case 'array':
            case 'json':
                // Array contains behavior
                if (is_array($value)) {
                    return [
                        'terms' => [
                            $field => $value
                        ]
                    ];
                }
                return [
                    'term' => [
                        $field => $value
                    ]
                ];

            case 'date':
            case 'datetime':
            case 'timestamp':
                // Support range queries for dates
                if (is_array($value)) {
                    $range = [];
                    if (isset($value['from'])) {
                        $range['gte'] = $value['from'];
                    }
                    if (isset($value['to'])) {
                        $range['lte'] = $value['to'];
                    }
                    return $range ? ['range' => [$field => $range]] : null;
                }
                // Exact date match
                return [
                    'term' => [
                        $field => $value
                    ]
                ];

            case 'boolean':
            case 'bool':
                // Boolean exact match
                return [
                    'term' => [
                        $field => filter_var($value, FILTER_VALIDATE_BOOLEAN)
                    ]
                ];

            case 'integer':
            case 'int':
            case 'float':
            case 'double':
            case 'decimal':
                // Support range queries for numeric fields
                if (is_array($value)) {
                    $range = [];
                    if (isset($value['min'])) {
                        $range['gte'] = $value['min'];
                    }
                    if (isset($value['max'])) {
                        $range['lte'] = $value['max'];
                    }
                    return $range ? ['range' => [$field => $range]] : null;
                }
                // Numeric exact match
                return [
                    'term' => [
                        $field => $value
                    ]
                ];

            default:
                // Fallback to term query for exact match
                return [
                    'term' => [
                        $field => $value
                    ]
                ];
        }
    }

    /**
     * Build sort from parameters.
     */
    protected function buildSort(array $sort): array
    {
        $result = [];
        foreach ($sort as $field => $order) {
            $result[] = [$field => ['order' => $order]];
        }
        return $result;
    }

    /**
     * Format response for pagination.
     */
    protected function formatPaginatedResponse(array $response, int $page, int $perPage): array
    {
        $hits = $response['hits'] ?? [];
        $total = $hits['total']['value'] ?? 0;
        $data = [];

        foreach ($hits['hits'] ?? [] as $hit) {
            $data[] = $hit['_source'] ?? [];
        }

        return [
            'data' => $data,
            'from' => ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get a single document by ID.
     */
    public function get(string $indexType, string $documentId): ?array
    {
        if (!$this->client) {
            return null;
        }

        try {
            $response = $this->client->get([
                'index' => $this->getIndexName($indexType),
                'id' => $documentId,
            ]);

            return $response['_source'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Aggregate data by field.
     */
    public function aggregate(string $indexType, string $aggregationType, string $field, array $filters = []): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $body = [
                'size' => 0,
                'aggs' => [
                    'result' => [
                        $aggregationType => ['field' => $field],
                    ],
                ],
            ];

            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters, $indexType);
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            $responseArray = $response->asArray();

            return [
                'value' => $responseArray['aggregations']['result']['value'] ?? 0,
                'total_documents' => $responseArray['hits']['total']['value'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error("Aggregation failed for {$indexType}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Group by field (terms aggregation).
     */
    public function groupBy(string $indexType, string $field, array $filters = [], int $size = 100): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $body = [
                'size' => 0,
                'aggs' => [
                    'groups' => [
                        'terms' => [
                            'field' => $field,
                            'size' => $size,
                        ],
                    ],
                ],
            ];

            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters, $indexType);
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            $responseArray = $response->asArray();
            $buckets = $responseArray['aggregations']['groups']['buckets'] ?? [];

            return array_map(fn($b) => [
                'key' => $b['key'],
                'count' => $b['doc_count'],
            ], $buckets);
        } catch (\Exception $e) {
            Log::error("Group by failed for {$indexType}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Count documents matching filters.
     */
    public function count(string $indexType, array $filters = []): int
    {
        if (!$this->client) {
            return 0;
        }

        try {
            $body = [];
            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters, $indexType);
            }

            $response = $this->client->count([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            return $response['count'] ?? 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Date histogram aggregation for trends.
     */
    public function dateHistogram(string $indexType, string $dateField, string $interval = 'day', array $filters = []): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $body = [
                'size' => 0,
                'aggs' => [
                    'timeline' => [
                        'date_histogram' => [
                            'field' => $dateField,
                            'calendar_interval' => $interval,
                            'format' => 'yyyy-MM-dd',
                        ],
                    ],
                ],
            ];

            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters, $indexType);
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            $responseArray = $response->asArray();
            $buckets = $responseArray['aggregations']['timeline']['buckets'] ?? [];

            return array_map(fn($b) => [
                'date' => $b['key_as_string'],
                'count' => $b['doc_count'],
            ], $buckets);
        } catch (\Exception $e) {
            Log::error("Date histogram failed for {$indexType}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    // ===== Convenience Methods for Specific Report Types =====

    /**
     * Search patients.
     */
    public function searchPatients(array $params = []): array
    {
        return $this->search(self::INDEX_PATIENT, $params);
    }

    /**
     * Search patient illness/diagnosis.
     */
    public function searchPatientIllness(array $params = []): array
    {
        return $this->search(self::INDEX_PATIENT_ILLNESS, $params);
    }

    /**
     * Search visit patients.
     */
    public function searchVisitPatients(array $params = []): array
    {
        return $this->search(self::INDEX_VISIT_PATIENT, $params);
    }

    /**
     * Search billings.
     */
    public function searchBillings(array $params = []): array
    {
        return $this->search(self::INDEX_BILLING, $params);
    }

    /**
     * Search invoices.
     */
    public function searchInvoices(array $params = []): array
    {
        return $this->search(self::INDEX_INVOICE, $params);
    }
}
