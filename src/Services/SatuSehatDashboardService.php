<?php

namespace Projects\WellmedBackbone\Services;

use Hanafalah\LaravelSupport\Concerns\Support\HasElasticsearchLog;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Projects\WellmedBackbone\Config\SatuSehat\SatuSehatDashboardConfig;

/**
 * Satu Sehat Dashboard Service
 *
 * Handles storage and retrieval of Satu Sehat integration metrics in Elasticsearch.
 * Tracks synced/unsynced counters for each resource type.
 *
 * ES stores only raw data (counts, timestamps). Presentation data (icons, colors, etc.)
 * is provided by SatuSehatDashboardConfig and merged on retrieval.
 *
 * Supports two modes:
 * 1. Current/Live data - Real-time counters without date filtering
 * 2. Monthly snapshots - Historical data segmented by month (for future use)
 */
class SatuSehatDashboardService
{
    use HasElasticsearchLog;

    protected $client;
    protected string $indexName = 'satu-sehat-dashboard';
    protected string $snapshotIndexName = 'satu-sehat-dashboard-snapshots';

    public function __construct()
    {
        if (config('elasticsearch.enabled', false)) {
            $this->client = app('elasticsearch');
        }
    }

    /**
     * Get all resource types configuration from config class
     *
     * @return array
     */
    public function getResourceTypes(): array
    {
        return SatuSehatDashboardConfig::getResourceTypes();
    }

    /**
     * Get example dashboard data from JSON file.
     * Useful for testing or when no data exists.
     *
     * @return array
     */
    public function getExampleData(): array
    {
        $jsonPath = __DIR__ . '/../Data/satu-sehat-dashboard-example.json';

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

        return [
            'success' => true,
            'data' => $data,
            'is_example' => true
        ];
    }

    /**
     * Get current/live dashboard data for a tenant (no date filtering).
     * This returns the real-time counters.
     * If index/document doesn't exist, it will be created with default values.
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function getCurrentDashboard(?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            if (!$this->client) {
                return [
                    'success' => false,
                    'error' => 'Elasticsearch client not available',
                    'data' => $this->mergeWithPresentationData($this->getDefaultDashboardData($tenantId ?? 0, $workspaceId))
                ];
            }

            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $documentId = $this->getDocumentId($tenantId, $workspaceId);

            $params = [
                'index' => $this->getIndexName(),
                'id' => $documentId
            ];

            try {
                $response = $this->client->get($params);

                return [
                    'success' => true,
                    'data' => $this->mergeWithPresentationData($response['_source'])
                ];
            } catch (\Throwable $e) {
                // Check if it's a 404 error (index or document not found)
                if (str_contains($e->getMessage(), '404') || str_contains($e->getMessage(), 'index_not_found')) {
                    // Initialize with default data and store to ES
                    return $this->initializeDashboard($tenantId, $workspaceId);
                }
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to retrieve current Satu Sehat dashboard', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => $this->mergeWithPresentationData($this->getDefaultDashboardData($tenantId ?? 0, $workspaceId))
            ];
        }
    }

    /**
     * Initialize dashboard with default values and store to Elasticsearch.
     * Called when index/document doesn't exist.
     *
     * @param int $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function initializeDashboard(int $tenantId, mixed $workspaceId): array
    {
        try {
            $defaultData = $this->getDefaultDashboardData($tenantId, $workspaceId);

            // Store default data to ES (this will create the index if it doesn't exist)
            $storeResult = $this->store($defaultData, $tenantId, $workspaceId);

            if (!$storeResult['success']) {
                Log::channel('elasticsearch')->warning('Failed to initialize dashboard in ES, returning defaults', [
                    'tenant_id' => $tenantId,
                    'error' => $storeResult['error'] ?? 'Unknown error'
                ]);
            } else {
                Log::channel('elasticsearch')->info('Dashboard initialized with default values', [
                    'tenant_id' => $tenantId,
                    'workspace_id' => $workspaceId
                ]);
            }

            return [
                'success' => true,
                'data' => $this->mergeWithPresentationData($defaultData),
                'message' => 'Dashboard initialized with default values',
                'initialized' => true
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to initialize dashboard', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId
            ]);

            // Return defaults even if store fails
            return [
                'success' => true,
                'data' => $this->mergeWithPresentationData($this->getDefaultDashboardData($tenantId, $workspaceId)),
                'message' => 'Returning defaults (failed to store)',
                'initialized' => false
            ];
        }
    }

    /**
     * Get dashboard data for a tenant.
     * If month is provided, returns monthly snapshot; otherwise returns current data.
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param string|null $month Filter by month (Y-m format) for historical data
     * @return array
     */
    public function getDashboard(?int $tenantId = null, mixed $workspaceId = null, ?string $month = null): array
    {
        // If no month filter, return current/live data
        if (!$month) {
            return $this->getCurrentDashboard($tenantId, $workspaceId);
        }

        // Otherwise, return monthly snapshot
        return $this->getMonthlySnapshot($tenantId, $workspaceId, $month);
    }

    /**
     * Get monthly snapshot for historical data
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param string $month Month in Y-m format
     * @return array
     */
    public function getMonthlySnapshot(?int $tenantId = null, mixed $workspaceId = null, string $month): array
    {
        try {
            if (!$this->client) {
                return [
                    'success' => false,
                    'error' => 'Elasticsearch client not available',
                    'data' => null
                ];
            }

            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $documentId = $this->getSnapshotDocumentId($tenantId, $workspaceId, $month);

            $params = [
                'index' => $this->getSnapshotIndexName(),
                'id' => $documentId
            ];

            try {
                $response = $this->client->get($params);

                return [
                    'success' => true,
                    'data' => $this->mergeWithPresentationData($response['_source']),
                    'is_snapshot' => true,
                    'month' => $month
                ];
            } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
                // Snapshot not found
                return [
                    'success' => true,
                    'data' => null,
                    'message' => "No snapshot found for {$month}",
                    'is_snapshot' => true,
                    'month' => $month
                ];
            }

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to retrieve monthly snapshot', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'month' => $month
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Store a monthly snapshot of current dashboard data.
     * Useful for historical comparison and reporting.
     *
     * @param string $month Month in Y-m format
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function storeMonthlySnapshot(string $month, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            if (!$this->client) {
                return [
                    'success' => false,
                    'error' => 'Elasticsearch client not available'
                ];
            }

            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Get current dashboard data
            $currentResult = $this->getCurrentDashboard($tenantId, $workspaceId);

            if (!$currentResult['success']) {
                return $currentResult;
            }

            $currentData = $currentResult['data'];

            // Prepare snapshot document
            $snapshot = [
                'tenant_id' => $tenantId,
                'workspace_id' => $workspaceId,
                'month' => $month,
                'integration_stats' => $currentData['integration_stats'] ?? [],
                'summary' => $currentData['summary'] ?? [],
                'snapshot_at' => now()->toIso8601String(),
                'created_at' => now()->toIso8601String(),
            ];

            $documentId = $this->getSnapshotDocumentId($tenantId, $workspaceId, $month);

            // Ensure snapshot index exists
            $this->ensureIndexExists($this->getSnapshotIndexName());

            $params = [
                'index' => $this->getSnapshotIndexName(),
                'id' => $documentId,
                'body' => $snapshot,
            ];

            $response = $this->client->index($params);

            Log::channel('elasticsearch')->info('Monthly snapshot stored', [
                'tenant_id' => $tenantId,
                'month' => $month,
                'document_id' => $response['_id'] ?? null
            ]);

            // Log to elasticsearch_logs table
            if (config('elasticsearch.logging.enabled', true)) {
                $this->logSingleOperation(
                    $this->getSnapshotIndexName(),
                    $documentId,
                    'index',
                    [
                        'result' => $response['result'] ?? 'created',
                        '_version' => $response['_version'] ?? 1
                    ]
                );
            }

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'month' => $month,
                'message' => "Snapshot for {$month} stored successfully"
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store monthly snapshot', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'month' => $month
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all available monthly snapshots for a tenant
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $limit Max number of snapshots to return
     * @return array
     */
    public function getAvailableSnapshots(?int $tenantId = null, mixed $workspaceId = null, int $limit = 12): array
    {
        try {
            if (!$this->client) {
                return [
                    'success' => false,
                    'error' => 'Elasticsearch client not available',
                    'data' => []
                ];
            }

            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $must = [
                ['term' => ['tenant_id' => $tenantId]]
            ];

            if ($workspaceId) {
                $must[] = ['term' => ['workspace_id' => $workspaceId]];
            }

            $params = [
                'index' => $this->getSnapshotIndexName(),
                'body' => [
                    'query' => [
                        'bool' => ['must' => $must]
                    ],
                    'sort' => [
                        ['month' => ['order' => 'desc']]
                    ],
                    '_source' => ['month', 'snapshot_at', 'summary']
                ],
                'size' => $limit
            ];

            $response = $this->client->search($params);

            $snapshots = [];
            foreach ($response['hits']['hits'] as $hit) {
                $snapshots[] = $hit['_source'];
            }

            return [
                'success' => true,
                'data' => $snapshots,
                'total' => $response['hits']['total']['value'] ?? 0
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get available snapshots', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Store or update dashboard data
     *
     * @param array $data
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function store(array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            if (!$this->client) {
                return [
                    'success' => false,
                    'error' => 'Elasticsearch client not available'
                ];
            }

            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Ensure index exists before storing
            $this->ensureIndexExists($this->getIndexName());

            $document = $this->prepareDocument($data, $tenantId, $workspaceId);
            $documentId = $this->getDocumentId($tenantId, $workspaceId);

            $params = [
                'index' => $this->getIndexName(),
                'id' => $documentId,
                'body' => $document,
            ];

            $response = $this->client->index($params);

            Log::channel('elasticsearch')->info('Satu Sehat dashboard stored', [
                'tenant_id' => $tenantId,
                'document_id' => $response['_id'] ?? null
            ]);

            // Log to elasticsearch_logs table
            if (config('elasticsearch.logging.enabled', true)) {
                $this->logSingleOperation(
                    $this->getIndexName(),
                    $documentId,
                    'index',
                    [
                        'result' => $response['result'] ?? 'created',
                        '_version' => $response['_version'] ?? 1
                    ]
                );
            }

            return [
                'success' => true,
                'id' => $response['_id'] ?? null,
                'index' => $response['_index'] ?? null,
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to store Satu Sehat dashboard', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update current count for a specific resource type.
     * This will recalculate synced/unsynced based on satuSehatCount.
     *
     * @param string $resourceType
     * @param int $currentCount The new current/wellMedCount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateCurrentCount(string $resourceType, int $currentCount, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Get current dashboard data
            $dashboardResult = $this->getDashboard($tenantId, $workspaceId);
            $dashboard = $dashboardResult['data'];

            // Find and update the resource
            $found = false;
            foreach ($dashboard['integration_stats'] as &$stat) {
                if ($stat['id'] === $resourceType) {
                    $stat['wellMedCount'] = $currentCount;
                    $stat['unsyncedCount'] = max(0, $currentCount - $stat['satuSehatCount']);
                    $stat['syncPercentage'] = $currentCount > 0
                        ? round(($stat['satuSehatCount'] / $currentCount) * 100, 1)
                        : 0;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return [
                    'success' => false,
                    'error' => "Resource type '{$resourceType}' not found"
                ];
            }

            // Recalculate summary
            $dashboard['summary'] = $this->calculateSummary($dashboard['integration_stats']);

            // Store updated dashboard
            return $this->store($dashboard, $tenantId, $workspaceId);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update current count', [
                'error' => $e->getMessage(),
                'resource_type' => $resourceType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update synced count for a specific resource type.
     * This will recalculate unsynced based on wellMedCount.
     *
     * @param string $resourceType
     * @param int $syncedCount The new satuSehatCount/syncedCount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateSyncedCount(string $resourceType, int $syncedCount, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Get current dashboard data
            $dashboardResult = $this->getDashboard($tenantId, $workspaceId);
            $dashboard = $dashboardResult['data'];

            // Find and update the resource
            $found = false;
            foreach ($dashboard['integration_stats'] as &$stat) {
                if ($stat['id'] === $resourceType) {
                    $stat['satuSehatCount'] = $syncedCount;
                    $stat['syncedCount'] = $syncedCount;
                    $stat['unsyncedCount'] = max(0, $stat['wellMedCount'] - $syncedCount);
                    $stat['syncPercentage'] = $stat['wellMedCount'] > 0
                        ? round(($syncedCount / $stat['wellMedCount']) * 100, 1)
                        : 0;
                    $stat['lastSync'] = now()->format('Y-m-d H:i');
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return [
                    'success' => false,
                    'error' => "Resource type '{$resourceType}' not found"
                ];
            }

            // Recalculate summary
            $dashboard['summary'] = $this->calculateSummary($dashboard['integration_stats']);

            // Store updated dashboard
            return $this->store($dashboard, $tenantId, $workspaceId);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update synced count', [
                'error' => $e->getMessage(),
                'resource_type' => $resourceType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Increment synced count for a resource type (called when sync succeeds)
     *
     * @param string $resourceType
     * @param int $increment Amount to increment (default 1)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function incrementSyncedCount(string $resourceType, int $increment = 1, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Get current dashboard data
            $dashboardResult = $this->getDashboard($tenantId, $workspaceId);
            $dashboard = $dashboardResult['data'];

            // Find and update the resource
            foreach ($dashboard['integration_stats'] as &$stat) {
                if ($stat['id'] === $resourceType) {
                    $stat['satuSehatCount'] += $increment;
                    $stat['syncedCount'] = $stat['satuSehatCount'];
                    $stat['unsyncedCount'] = max(0, $stat['wellMedCount'] - $stat['satuSehatCount']);
                    $stat['syncPercentage'] = $stat['wellMedCount'] > 0
                        ? round(($stat['satuSehatCount'] / $stat['wellMedCount']) * 100, 1)
                        : 0;
                    $stat['lastSync'] = now()->format('Y-m-d H:i');
                    break;
                }
            }

            // Recalculate summary
            $dashboard['summary'] = $this->calculateSummary($dashboard['integration_stats']);

            // Store updated dashboard
            return $this->store($dashboard, $tenantId, $workspaceId);

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Increment current/wellMed count for a resource type (called when record is created in WellMed)
     *
     * @param string $resourceType
     * @param int $increment Amount to increment (default 1)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function incrementCurrentCount(string $resourceType, int $increment = 1, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Get current dashboard data
            $dashboardResult = $this->getDashboard($tenantId, $workspaceId);
            $dashboard = $dashboardResult['data'];

            // Find and update the resource
            foreach ($dashboard['integration_stats'] as &$stat) {
                if ($stat['id'] === $resourceType) {
                    $stat['wellMedCount'] += $increment;
                    $stat['unsyncedCount'] = max(0, $stat['wellMedCount'] - $stat['satuSehatCount']);
                    $stat['syncPercentage'] = $stat['wellMedCount'] > 0
                        ? round(($stat['satuSehatCount'] / $stat['wellMedCount']) * 100, 1)
                        : 0;
                    break;
                }
            }

            // Recalculate summary
            $dashboard['summary'] = $this->calculateSummary($dashboard['integration_stats']);

            // Store updated dashboard
            return $this->store($dashboard, $tenantId, $workspaceId);

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Bulk update multiple resource counts at once
     *
     * @param array $updates Array of ['resource_type' => ['current' => x, 'synced' => y]]
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function bulkUpdate(array $updates, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            // Get current dashboard data
            $dashboardResult = $this->getDashboard($tenantId, $workspaceId);
            $dashboard = $dashboardResult['data'];

            // Update each resource
            foreach ($dashboard['integration_stats'] as &$stat) {
                if (isset($updates[$stat['id']])) {
                    $update = $updates[$stat['id']];

                    if (isset($update['current'])) {
                        $stat['wellMedCount'] = $update['current'];
                    }
                    if (isset($update['synced'])) {
                        $stat['satuSehatCount'] = $update['synced'];
                        $stat['syncedCount'] = $update['synced'];
                        $stat['lastSync'] = now()->format('Y-m-d H:i');
                    }

                    // Recalculate derived values
                    $stat['unsyncedCount'] = max(0, $stat['wellMedCount'] - $stat['satuSehatCount']);
                    $stat['syncPercentage'] = $stat['wellMedCount'] > 0
                        ? round(($stat['satuSehatCount'] / $stat['wellMedCount']) * 100, 1)
                        : 0;
                }
            }

            // Recalculate summary
            $dashboard['summary'] = $this->calculateSummary($dashboard['integration_stats']);

            // Store updated dashboard
            return $this->store($dashboard, $tenantId, $workspaceId);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to bulk update dashboard', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate summary statistics from integration stats
     *
     * @param array $integrationStats
     * @return array
     */
    protected function calculateSummary(array $integrationStats): array
    {
        $total = [
            'wellMed' => 0,
            'satuSehat' => 0,
            'unsynced' => 0,
            'synced' => 0
        ];

        foreach ($integrationStats as $stat) {
            $total['wellMed'] += $stat['wellMedCount'] ?? 0;
            $total['satuSehat'] += $stat['satuSehatCount'] ?? 0;
            $total['unsynced'] += $stat['unsyncedCount'] ?? 0;
            $total['synced'] += $stat['syncedCount'] ?? $stat['satuSehatCount'] ?? 0;
        }

        $total['percentage'] = $total['wellMed'] > 0
            ? round(($total['satuSehat'] / $total['wellMed']) * 100, 1)
            : 0;

        return $total;
    }

    /**
     * Get default dashboard raw data structure (for ES storage).
     * Only stores counts and IDs - no presentation data.
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    protected function getDefaultDashboardData(?int $tenantId, mixed $workspaceId): array
    {
        $integrationStats = [];
        $resourceTypeIds = SatuSehatDashboardConfig::getResourceTypeIds();

        foreach ($resourceTypeIds as $id) {
            $integrationStats[] = [
                'id' => $id,
                'wellMedCount' => 0,
                'satuSehatCount' => 0,
                'unsyncedCount' => 0,
                'syncedCount' => 0,
                'syncPercentage' => 0,
                'lastSync' => null,
            ];
        }

        return [
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'integration_stats' => $integrationStats,
            'summary' => [
                'wellMed' => 0,
                'satuSehat' => 0,
                'unsynced' => 0,
                'synced' => 0,
                'percentage' => 0
            ],
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Merge raw ES data with presentation data from config.
     * Called when returning data to the client.
     *
     * @param array $rawData Raw data from ES
     * @return array Data with presentation info merged
     */
    protected function mergeWithPresentationData(array $rawData): array
    {
        $resourceTypes = SatuSehatDashboardConfig::getResourceTypes();

        // Merge presentation data into integration_stats
        if (isset($rawData['integration_stats'])) {
            foreach ($rawData['integration_stats'] as &$stat) {
                $id = $stat['id'] ?? null;
                if ($id && isset($resourceTypes[$id])) {
                    $config = $resourceTypes[$id];
                    $stat['category'] = $config['category'];
                    $stat['name'] = $config['name'];
                    $stat['icon'] = $config['icon'];
                    $stat['color'] = $config['color'];
                    $stat['gradient'] = $config['gradient'] ?? null;
                    $stat['bg_light'] = $config['bg_light'] ?? null;
                    $stat['text_color'] = $config['text_color'] ?? null;
                    $stat['border_color'] = $config['border_color'] ?? null;
                    $stat['description'] = $config['description'];
                    $stat['loggerPath'] = $config['loggerPath'] ?? null;
                }
            }
        }

        return $rawData;
    }

    /**
     * Prepare document for Elasticsearch
     *
     * @param array $data
     * @param int $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    protected function prepareDocument(array $data, int $tenantId, mixed $workspaceId): array
    {
        return [
            'tenant_id' => $tenantId,
            'workspace_id' => $workspaceId,
            'integration_stats' => $data['integration_stats'] ?? [],
            'summary' => $data['summary'] ?? $this->calculateSummary($data['integration_stats'] ?? []),
            'updated_at' => now()->toIso8601String(),
            'created_at' => $data['created_at'] ?? now()->toIso8601String(),
        ];
    }

    /**
     * Get document ID for upsert operations
     *
     * @param int $tenantId
     * @param mixed $workspaceId
     * @return string
     */
    protected function getDocumentId(int $tenantId, mixed $workspaceId): string
    {
        return "dashboard_{$tenantId}_{$workspaceId}";
    }

    /**
     * Get document ID for monthly snapshot
     *
     * @param int $tenantId
     * @param mixed $workspaceId
     * @param string $month
     * @return string
     */
    protected function getSnapshotDocumentId(int $tenantId, mixed $workspaceId, string $month): string
    {
        return "snapshot_{$tenantId}_{$workspaceId}_{$month}";
    }

    /**
     * Get Elasticsearch index name
     *
     * @return string
     */
    protected function getIndexName(): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');

        return $prefix . $separator . $this->indexName;
    }

    /**
     * Get Elasticsearch index name for snapshots
     *
     * @return string
     */
    protected function getSnapshotIndexName(): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');

        return $prefix . $separator . $this->snapshotIndexName;
    }

    /**
     * Ensure the Elasticsearch index exists, create if not.
     *
     * @param string $indexName
     * @return bool
     */
    protected function ensureIndexExists(string $indexName): bool
    {
        try {
            if (!$this->client) {
                Log::channel('elasticsearch')->warning('Elasticsearch client not available, skipping index check', [
                    'index' => $indexName
                ]);
                return false;
            }

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
                                'workspace_id' => ['type' => 'string'],
                                'integration_stats' => ['type' => 'nested'],
                                'summary' => ['type' => 'object'],
                                'updated_at' => ['type' => 'date'],
                                'created_at' => ['type' => 'date'],
                                'month' => ['type' => 'keyword'],
                                'snapshot_at' => ['type' => 'date']
                            ]
                        ]
                    ]
                ]);

                Log::channel('elasticsearch')->info('Created Elasticsearch index', [
                    'index' => $indexName
                ]);
            }

            return true;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to ensure index exists', [
                'index' => $indexName,
                'error' => $e->getMessage()
            ]);

            // Return true to allow the operation to continue
            // Elasticsearch might auto-create the index on first document
            return true;
        }
    }

    /**
     * Delete dashboard data for a tenant
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function delete(?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            if (!$this->client) {
                return [
                    'success' => false,
                    'error' => 'Elasticsearch client not available'
                ];
            }

            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();

            $documentId = $this->getDocumentId($tenantId, $workspaceId);

            $params = [
                'index' => $this->getIndexName(),
                'id' => $documentId
            ];

            $response = $this->client->delete($params);

            return [
                'success' => true,
                'deleted' => $response['result'] === 'deleted'
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
