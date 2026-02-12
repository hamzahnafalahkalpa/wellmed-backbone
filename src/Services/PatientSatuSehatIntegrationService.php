<?php

namespace Projects\WellmedBackbone\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Patient Satu Sehat Integration Service
 *
 * Handles storage and retrieval of patient-level Satu Sehat integration status.
 * Uses a SINGLE shared index per tenant with one document per patient.
 *
 * Index pattern: {prefix}.{tenant}.patient-satu-sehat-integration
 * Document ID: {patient_id}
 *
 * This is NOT time-based - it stores the CURRENT state of each patient's
 * integration status with Satu Sehat (IHS number, sync progress, etc.)
 */
class PatientSatuSehatIntegrationService
{
    protected $client;
    protected string $indexPrefix = 'patient-satu-sehat-integration';

    /**
     * Sync statuses
     */
    public const SYNC_STATUS_PENDING = 'pending';
    public const SYNC_STATUS_IN_PROGRESS = 'in_progress';
    public const SYNC_STATUS_COMPLETED = 'completed';
    public const SYNC_STATUS_FAILED = 'failed';

    public function __construct()
    {
        $this->client = app('elasticsearch');
    }

    // =========================================================================
    // Public API Methods
    // =========================================================================

    /**
     * Get patient integration status.
     *
     * @param string $patientId
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function get(string $patientId, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $response = $this->client->get([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId)
            ]);

            return [
                'success' => true,
                'data' => $response['_source'] ?? null
            ];

        } catch (\Throwable $e) {
            // Document not found - return default
            if (str_contains($e->getMessage(), 'not_found') || str_contains($e->getMessage(), '404')) {
                return [
                    'success' => true,
                    'data' => $this->getDefaultDocument($patientId, $tenantId, $workspaceId)
                ];
            }

            Log::channel('elasticsearch')->error('Failed to get patient integration status', [
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
     * Store/update patient integration status (upsert).
     *
     * @param string $patientId
     * @param array $data
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function store(string $patientId, array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $this->ensureIndexExists($tenantId);

            // Get existing or create default
            $existing = $this->getExistingDocument($patientId, $tenantId);
            $document = $this->mergeWithExisting($existing, $data, $patientId, $tenantId, $workspaceId);

            $response = $this->client->index([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId),
                'body' => $document
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'index' => $response['_index'] ?? null
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store patient integration status', [
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
     * Update patient IHS number.
     *
     * @param string $patientId
     * @param string $ihsNumber
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateIhsNumber(
        string $patientId,
        string $ihsNumber,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        return $this->store($patientId, [
            'general' => ['ihs_number' => $ihsNumber]
        ], $tenantId, $workspaceId);
    }

    /**
     * Update sync counter for a specific type.
     *
     * @param string $patientId
     * @param string $syncFlag encounter|dispense|condition|observation|patient|location|practitioner
     * @param int $from Synced count
     * @param int $to Total count
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateSyncCounter(
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

            $this->ensureIndexExists($tenantId);

            $existing = $this->getExistingDocument($patientId, $tenantId);
            $document = $existing ?? $this->getDefaultDocument($patientId, $tenantId, $workspaceId);

            // Update specific sync counter
            foreach ($document['syncs'] as &$sync) {
                if ($sync['flag'] === $syncFlag) {
                    $sync['from'] = $from;
                    $sync['to'] = $to;
                    $sync['progress'] = $to > 0 ? round(($from / $to) * 100, 2) : 0;
                    $sync['last_synced_at'] = now()->toIso8601String();
                    break;
                }
            }

            // Recalculate overall progress
            $document = $this->recalculateOverallProgress($document);
            $document['updated_at'] = now()->toIso8601String();

            $response = $this->client->index([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId),
                'body' => $document
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null
            ];

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
     * Increment sync counter.
     *
     * @param string $patientId
     * @param string $syncFlag
     * @param bool $success Whether the sync was successful
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function incrementSyncCounter(
        string $patientId,
        string $syncFlag,
        bool $success = true,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $this->ensureIndexExists($tenantId);

            $existing = $this->getExistingDocument($patientId, $tenantId);
            $document = $existing ?? $this->getDefaultDocument($patientId, $tenantId, $workspaceId);

            // Update specific sync counter
            foreach ($document['syncs'] as &$sync) {
                if ($sync['flag'] === $syncFlag) {
                    if ($success) {
                        $sync['from'] = ($sync['from'] ?? 0) + 1;
                        $sync['success_count'] = ($sync['success_count'] ?? 0) + 1;
                    } else {
                        $sync['failed_count'] = ($sync['failed_count'] ?? 0) + 1;
                    }
                    $sync['to'] = ($sync['to'] ?? 0) + 1;
                    $sync['progress'] = $sync['to'] > 0 ? round(($sync['from'] / $sync['to']) * 100, 2) : 0;
                    $sync['last_synced_at'] = now()->toIso8601String();
                    break;
                }
            }

            // Recalculate overall progress
            $document = $this->recalculateOverallProgress($document);
            $document['updated_at'] = now()->toIso8601String();

            $response = $this->client->index([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId),
                'body' => $document
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null
            ];

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
     * @param string $logType
     * @param string $message
     * @param array $context
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function addLog(
        string $patientId,
        string $logType,
        string $message,
        array $context = [],
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $this->ensureIndexExists($tenantId);

            $existing = $this->getExistingDocument($patientId, $tenantId);
            $document = $existing ?? $this->getDefaultDocument($patientId, $tenantId, $workspaceId);

            // Add log entry (keep last 50 logs)
            $document['logs'][] = [
                'type' => $logType,
                'message' => $message,
                'context' => $context,
                'timestamp' => now()->toIso8601String()
            ];

            // Keep only last 50 logs
            if (count($document['logs']) > 50) {
                $document['logs'] = array_slice($document['logs'], -50);
            }

            $document['updated_at'] = now()->toIso8601String();

            $response = $this->client->index([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId),
                'body' => $document
            ]);

            return [
                'success' => true,
                'id' => $response['_id'] ?? null
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to add patient integration log', [
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
     * Search patients by integration status.
     *
     * @param array $filters
     * @param int|null $tenantId
     * @return array
     */
    public function search(array $filters = [], ?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();

            $must = [
                ['term' => ['tenant_id' => $tenantId]]
            ];

            // Filter by IHS number existence
            if (isset($filters['has_ihs_number'])) {
                if ($filters['has_ihs_number']) {
                    $must[] = ['exists' => ['field' => 'general.ihs_number']];
                } else {
                    $must[] = ['bool' => ['must_not' => ['exists' => ['field' => 'general.ihs_number']]]];
                }
            }

            // Filter by sync progress
            if (isset($filters['min_progress'])) {
                $must[] = ['range' => ['progress' => ['gte' => $filters['min_progress']]]];
            }

            if (isset($filters['max_progress'])) {
                $must[] = ['range' => ['progress' => ['lte' => $filters['max_progress']]]];
            }

            // Filter by specific sync flag status
            if (isset($filters['sync_flag']) && isset($filters['sync_status'])) {
                $must[] = [
                    'nested' => [
                        'path' => 'syncs',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['syncs.flag' => $filters['sync_flag']]],
                                    ['range' => ['syncs.progress' => ['gte' => $filters['sync_status'] === 'completed' ? 100 : 0]]]
                                ]
                            ]
                        ]
                    ]
                ];
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($tenantId),
                'body' => [
                    'query' => ['bool' => ['must' => $must]],
                    'size' => $filters['size'] ?? 100,
                    'from' => $filters['from'] ?? 0,
                    'sort' => [['updated_at' => ['order' => 'desc']]]
                ]
            ]);

            $results = [];
            foreach ($response['hits']['hits'] ?? [] as $hit) {
                $results[] = $hit['_source'];
            }

            return [
                'success' => true,
                'data' => $results,
                'total' => $response['hits']['total']['value'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to search patient integrations', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get integration statistics for a tenant.
     *
     * @param int|null $tenantId
     * @return array
     */
    public function getStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();

            $response = $this->client->search([
                'index' => $this->getIndexName($tenantId),
                'body' => [
                    'size' => 0,
                    'query' => ['term' => ['tenant_id' => $tenantId]],
                    'aggs' => [
                        'total_patients' => ['value_count' => ['field' => 'patient_id.keyword']],
                        'with_ihs_number' => [
                            'filter' => ['exists' => ['field' => 'general.ihs_number']]
                        ],
                        'avg_progress' => ['avg' => ['field' => 'progress']],
                        'fully_synced' => [
                            'filter' => ['range' => ['progress' => ['gte' => 100]]]
                        ],
                        'not_synced' => [
                            'filter' => ['term' => ['progress' => 0]]
                        ]
                    ]
                ]
            ]);

            $aggs = $response['aggregations'] ?? [];

            return [
                'success' => true,
                'data' => [
                    'total_patients' => $aggs['total_patients']['value'] ?? 0,
                    'with_ihs_number' => $aggs['with_ihs_number']['doc_count'] ?? 0,
                    'avg_progress' => round($aggs['avg_progress']['value'] ?? 0, 2),
                    'fully_synced' => $aggs['fully_synced']['doc_count'] ?? 0,
                    'not_synced' => $aggs['not_synced']['doc_count'] ?? 0
                ]
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get integration statistics', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete patient integration data.
     *
     * @param string $patientId
     * @param int|null $tenantId
     * @return array
     */
    public function delete(string $patientId, ?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();

            $response = $this->client->delete([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId)
            ]);

            return [
                'success' => true,
                'deleted' => $response['result'] === 'deleted'
            ];

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'not_found')) {
                return ['success' => true, 'deleted' => false];
            }

            Log::channel('elasticsearch')->error('Failed to delete patient integration', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // Internal Methods
    // =========================================================================

    /**
     * Get index name for tenant.
     */
    protected function getIndexName(int $tenantId): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');

        return "{$prefix}{$separator}{$tenantId}{$separator}{$this->indexPrefix}";
    }

    /**
     * Generate document ID for patient.
     */
    protected function generateDocumentId(string $patientId): string
    {
        return $patientId;
    }

    /**
     * Get existing document if exists.
     */
    protected function getExistingDocument(string $patientId, int $tenantId): ?array
    {
        try {
            $response = $this->client->get([
                'index' => $this->getIndexName($tenantId),
                'id' => $this->generateDocumentId($patientId)
            ]);

            return $response['_source'] ?? null;

        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Merge new data with existing document.
     */
    protected function mergeWithExisting(?array $existing, array $newData, string $patientId, int $tenantId, mixed $workspaceId): array
    {
        $document = $existing ?? $this->getDefaultDocument($patientId, $tenantId, $workspaceId);

        // Merge general data
        if (isset($newData['general'])) {
            $document['general'] = array_merge($document['general'] ?? [], $newData['general']);
        }

        // Merge syncs by flag
        if (isset($newData['syncs'])) {
            $existingSyncs = [];
            foreach ($document['syncs'] as $sync) {
                $existingSyncs[$sync['flag']] = $sync;
            }

            foreach ($newData['syncs'] as $newSync) {
                if (isset($newSync['flag'])) {
                    $existingSyncs[$newSync['flag']] = array_merge(
                        $existingSyncs[$newSync['flag']] ?? [],
                        $newSync
                    );
                }
            }

            $document['syncs'] = array_values($existingSyncs);
        }

        // Merge logs (append)
        if (isset($newData['logs'])) {
            $document['logs'] = array_merge($document['logs'] ?? [], $newData['logs']);
        }

        $document['updated_at'] = now()->toIso8601String();

        return $this->recalculateOverallProgress($document);
    }

    /**
     * Recalculate overall progress from syncs.
     */
    protected function recalculateOverallProgress(array $document): array
    {
        $totalFrom = 0;
        $totalTo = 0;

        foreach ($document['syncs'] ?? [] as $sync) {
            $totalFrom += $sync['from'] ?? 0;
            $totalTo += $sync['to'] ?? 0;
        }

        $document['total_synced'] = $totalFrom;
        $document['total_to_sync'] = $totalTo;
        $document['progress'] = $totalTo > 0 ? round(($totalFrom / $totalTo) * 100, 2) : 0;

        return $document;
    }

    /**
     * Get default document structure.
     */
    protected function getDefaultDocument(string $patientId, int $tenantId, mixed $workspaceId): array
    {
        $now = now()->toIso8601String();

        return [
            'patient_id' => $patientId,
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'flag' => 'satu-sehat',
            'progress' => 0,
            'total_synced' => 0,
            'total_to_sync' => 0,
            'general' => [
                'ihs_number' => null,
                'nik' => null,
                'name' => null
            ],
            'syncs' => [
                [
                    'flag' => 'patient',
                    'label' => 'Data Pasien',
                    'progress' => 0,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'last_synced_at' => null
                ],
                [
                    'flag' => 'encounter',
                    'label' => 'Kunjungan',
                    'progress' => 0,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'last_synced_at' => null
                ],
                [
                    'flag' => 'condition',
                    'label' => 'Diagnosa',
                    'progress' => 0,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'last_synced_at' => null
                ],
                [
                    'flag' => 'observation',
                    'label' => 'Observasi',
                    'progress' => 0,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'last_synced_at' => null
                ],
                [
                    'flag' => 'dispense',
                    'label' => 'Resep/Obat',
                    'progress' => 0,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'last_synced_at' => null
                ],
                [
                    'flag' => 'procedure',
                    'label' => 'Tindakan',
                    'progress' => 0,
                    'from' => 0,
                    'to' => 0,
                    'success_count' => 0,
                    'failed_count' => 0,
                    'last_synced_at' => null
                ]
            ],
            'logs' => [],
            'created_at' => $now,
            'updated_at' => $now
        ];
    }

    /**
     * Ensure index exists with proper mappings.
     */
    protected function ensureIndexExists(int $tenantId): bool
    {
        try {
            $indexName = $this->getIndexName($tenantId);

            $exists = $this->client->indices()->exists(['index' => $indexName]);
            $indexExists = is_bool($exists) ? $exists : (method_exists($exists, 'asBool') ? $exists->asBool() : (bool) $exists);

            if (!$indexExists) {
                $this->client->indices()->create([
                    'index' => $indexName,
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 1,
                            'number_of_replicas' => 0
                        ],
                        'mappings' => [
                            'properties' => [
                                'patient_id' => ['type' => 'keyword'],
                                'tenant_id' => ['type' => 'integer'],
                                'workspace_id' => ['type' => 'keyword'],
                                'flag' => ['type' => 'keyword'],
                                'progress' => ['type' => 'float'],
                                'total_synced' => ['type' => 'integer'],
                                'total_to_sync' => ['type' => 'integer'],
                                'general' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'ihs_number' => ['type' => 'keyword'],
                                        'nik' => ['type' => 'keyword'],
                                        'name' => ['type' => 'text']
                                    ]
                                ],
                                'syncs' => [
                                    'type' => 'nested',
                                    'properties' => [
                                        'flag' => ['type' => 'keyword'],
                                        'label' => ['type' => 'keyword'],
                                        'progress' => ['type' => 'float'],
                                        'from' => ['type' => 'integer'],
                                        'to' => ['type' => 'integer'],
                                        'success_count' => ['type' => 'integer'],
                                        'failed_count' => ['type' => 'integer'],
                                        'last_synced_at' => ['type' => 'date']
                                    ]
                                ],
                                'logs' => [
                                    'type' => 'nested',
                                    'properties' => [
                                        'type' => ['type' => 'keyword'],
                                        'message' => ['type' => 'text'],
                                        'timestamp' => ['type' => 'date']
                                    ]
                                ],
                                'created_at' => ['type' => 'date'],
                                'updated_at' => ['type' => 'date']
                            ]
                        ]
                    ]
                ]);

                Log::channel('elasticsearch')->info('Created patient satu sehat integration index', [
                    'index' => $indexName
                ]);
            }

            return true;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to ensure index exists', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);
            return true;
        }
    }
}
