<?php

namespace Projects\WellmedBackbone\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Flush old Elasticsearch indices based on retention policy.
 *
 * This command handles cleanup of:
 * - Per-patient daily indices (patient-dashboard-metrics-{patientId}-daily)
 * - Old documents in shared dashboard indices
 *
 * Usage:
 *   php artisan wellmed-backbone:flush-elasticsearch-indices
 *   php artisan wellmed-backbone:flush-elasticsearch-indices --dry-run
 *   php artisan wellmed-backbone:flush-elasticsearch-indices --type=patient-dashboard-metrics
 *   php artisan wellmed-backbone:flush-elasticsearch-indices --days=2
 */
class FlushElasticsearchIndicesCommand extends Command
{
    protected $signature = 'wellmed-backbone:flush-elasticsearch-indices
                            {--type= : Specific index type to flush (e.g., patient-dashboard-metrics)}
                            {--days= : Override retention days from config}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Skip confirmation prompt}
                            {--tenant= : Specific tenant ID to flush (optional)}';

    protected $description = 'Flush old Elasticsearch indices based on retention policy';

    protected $client;
    protected array $stats = [
        'indices_deleted' => 0,
        'documents_deleted' => 0,
        'indices_skipped' => 0,
        'errors' => [],
    ];

    public function handle(): int
    {
        $this->client = app('elasticsearch');

        if (!config('elasticsearch.enabled', true)) {
            $this->error('Elasticsearch is disabled.');
            return Command::FAILURE;
        }

        if (!config('elasticsearch.retention.enabled', true)) {
            $this->error('Elasticsearch retention is disabled in config.');
            return Command::FAILURE;
        }

        $isDryRun = $this->option('dry-run');
        $specificType = $this->option('type');
        $overrideDays = $this->option('days');
        $tenantId = $this->option('tenant');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $this->info('Starting Elasticsearch indices cleanup...');
        $this->newLine();

        // Get retention config
        $retentionConfig = config('elasticsearch.retention.indices', []);

        if ($specificType) {
            // Flush specific type only
            if (!isset($retentionConfig[$specificType])) {
                $this->error("Unknown index type: {$specificType}");
                $this->info('Available types: ' . implode(', ', array_keys($retentionConfig)));
                return Command::FAILURE;
            }

            $this->flushIndexType($specificType, $retentionConfig[$specificType], $overrideDays, $isDryRun, $tenantId);
        } else {
            // Flush all flushable types
            foreach ($retentionConfig as $type => $config) {
                if (!($config['flushable'] ?? false)) {
                    $this->line("⏭️  Skipping {$type} (not flushable)");
                    continue;
                }

                $this->flushIndexType($type, $config, $overrideDays, $isDryRun, $tenantId);
            }
        }

        $this->newLine();
        $this->displaySummary($isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Flush indices of a specific type.
     */
    protected function flushIndexType(string $type, array $config, ?string $overrideDays, bool $isDryRun, ?string $tenantId): void
    {
        $retentionDays = $overrideDays ? (int) $overrideDays : ($config['retention_days'] ?? 7);
        $cutoffDate = Carbon::now()->subDays($retentionDays)->startOfDay();

        $this->info("📦 Processing: {$type}");
        $this->line("   Retention: {$retentionDays} days (cutoff: {$cutoffDate->toDateString()})");
        $this->line("   Description: " . ($config['description'] ?? 'N/A'));

        // Handle per-patient indices differently
        if ($type === 'patient-dashboard-metrics') {
            $this->flushPatientDashboardMetrics($cutoffDate, $config, $isDryRun, $tenantId);
        } else {
            $this->flushSharedIndex($type, $cutoffDate, $config, $isDryRun, $tenantId);
        }

        $this->newLine();
    }

    /**
     * Flush per-patient dashboard metrics indices.
     *
     * These indices have the pattern: {prefix}.{tenant}.patient-dashboard-metrics-{patientId}-daily
     */
    protected function flushPatientDashboardMetrics(Carbon $cutoffDate, array $config, bool $isDryRun, ?string $tenantId): void
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $pattern = $tenantId
            ? "{$prefix}.{$tenantId}.patient-dashboard-metrics-*-daily"
            : "{$prefix}.*.patient-dashboard-metrics-*-daily";

        $this->line("   Pattern: {$pattern}");

        try {
            // Get all matching indices
            $response = $this->client->cat()->indices([
                'index' => $pattern,
                'format' => 'json',
                'h' => 'index,docs.count,store.size,creation.date.string'
            ]);

            $indices = $response->asArray();

            if (empty($indices)) {
                $this->line('   No matching indices found.');
                return;
            }

            $this->line("   Found " . count($indices) . " indices");

            $toDelete = [];
            $toKeep = [];

            foreach ($indices as $indexInfo) {
                $indexName = $indexInfo['index'];

                // Parse the index name to extract date info
                // Format: {prefix}.{tenant}.patient-dashboard-metrics-{patientId}-daily
                // Documents inside have date field

                // Check if index has any recent documents
                $hasRecentDocs = $this->indexHasRecentDocuments($indexName, $cutoffDate);

                if (!$hasRecentDocs) {
                    $toDelete[] = $indexInfo;
                } else {
                    $toKeep[] = $indexName;

                    // If configured to flush documents but keep index, delete old docs
                    if ($config['flush_documents'] ?? false) {
                        $this->flushOldDocumentsFromIndex($indexName, $cutoffDate, $isDryRun);
                    }
                }
            }

            // Delete empty/old indices
            if ($config['flush_indices'] ?? false) {
                foreach ($toDelete as $indexInfo) {
                    $indexName = $indexInfo['index'];
                    $size = $indexInfo['store.size'] ?? 'unknown';
                    $docsCount = $indexInfo['docs.count'] ?? 0;

                    if ($isDryRun) {
                        $this->line("   [DRY-RUN] Would delete index: {$indexName} ({$docsCount} docs, {$size})");
                    } else {
                        try {
                            $this->client->indices()->delete(['index' => $indexName]);
                            $this->line("   ✅ Deleted index: {$indexName} ({$docsCount} docs, {$size})");
                            $this->stats['indices_deleted']++;
                        } catch (\Throwable $e) {
                            $this->error("   ❌ Failed to delete {$indexName}: " . $e->getMessage());
                            $this->stats['errors'][] = "Delete {$indexName}: " . $e->getMessage();
                        }
                    }
                }
            }

            $this->line("   Kept " . count($toKeep) . " indices with recent data");

        } catch (\Throwable $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
            $this->stats['errors'][] = "Pattern {$pattern}: " . $e->getMessage();
        }
    }

    /**
     * Check if an index has documents newer than cutoff date.
     */
    protected function indexHasRecentDocuments(string $indexName, Carbon $cutoffDate): bool
    {
        try {
            $response = $this->client->search([
                'index' => $indexName,
                'body' => [
                    'query' => [
                        'range' => [
                            'timestamp' => [
                                'gte' => $cutoffDate->toIso8601String()
                            ]
                        ]
                    ],
                    'size' => 0
                ]
            ]);

            $count = $response['hits']['total']['value'] ?? 0;
            return $count > 0;

        } catch (\Throwable $e) {
            // If we can't check, assume it has recent docs (safer)
            return true;
        }
    }

    /**
     * Delete old documents from an index while keeping recent ones.
     */
    protected function flushOldDocumentsFromIndex(string $indexName, Carbon $cutoffDate, bool $isDryRun): void
    {
        try {
            // First, count how many documents will be deleted
            $countResponse = $this->client->count([
                'index' => $indexName,
                'body' => [
                    'query' => [
                        'range' => [
                            'timestamp' => [
                                'lt' => $cutoffDate->toIso8601String()
                            ]
                        ]
                    ]
                ]
            ]);

            $toDeleteCount = $countResponse['count'] ?? 0;

            if ($toDeleteCount === 0) {
                return;
            }

            if ($isDryRun) {
                $this->line("   [DRY-RUN] Would delete {$toDeleteCount} old documents from {$indexName}");
                return;
            }

            // Delete old documents
            $response = $this->client->deleteByQuery([
                'index' => $indexName,
                'body' => [
                    'query' => [
                        'range' => [
                            'timestamp' => [
                                'lt' => $cutoffDate->toIso8601String()
                            ]
                        ]
                    ]
                ]
            ]);

            $deleted = $response['deleted'] ?? 0;
            $this->stats['documents_deleted'] += $deleted;

            if ($deleted > 0) {
                $this->line("   🗑️  Deleted {$deleted} old documents from {$indexName}");
            }

        } catch (\Throwable $e) {
            $this->error("   ❌ Failed to flush documents from {$indexName}: " . $e->getMessage());
            $this->stats['errors'][] = "Flush docs {$indexName}: " . $e->getMessage();
        }
    }

    /**
     * Flush documents from shared dashboard indices.
     */
    protected function flushSharedIndex(string $type, Carbon $cutoffDate, array $config, bool $isDryRun, ?string $tenantId): void
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');

        // Build index pattern
        $pattern = $tenantId
            ? "{$prefix}{$separator}{$tenantId}{$separator}{$type}"
            : "{$prefix}{$separator}*{$separator}{$type}";

        $this->line("   Pattern: {$pattern}");

        if (!($config['flush_documents'] ?? false)) {
            $this->line("   Document flushing disabled for this type");
            return;
        }

        try {
            // Count documents to delete
            $countResponse = $this->client->count([
                'index' => $pattern,
                'body' => [
                    'query' => [
                        'range' => [
                            'timestamp' => [
                                'lt' => $cutoffDate->toIso8601String()
                            ]
                        ]
                    ]
                ]
            ]);

            $toDeleteCount = $countResponse['count'] ?? 0;

            if ($toDeleteCount === 0) {
                $this->line("   No old documents to delete");
                return;
            }

            if ($isDryRun) {
                $this->line("   [DRY-RUN] Would delete {$toDeleteCount} documents older than {$cutoffDate->toDateString()}");
                return;
            }

            // Delete old documents
            $response = $this->client->deleteByQuery([
                'index' => $pattern,
                'body' => [
                    'query' => [
                        'range' => [
                            'timestamp' => [
                                'lt' => $cutoffDate->toIso8601String()
                            ]
                        ]
                    ]
                ]
            ]);

            $deleted = $response['deleted'] ?? 0;
            $this->stats['documents_deleted'] += $deleted;

            $this->line("   🗑️  Deleted {$deleted} documents older than {$cutoffDate->toDateString()}");

        } catch (\Throwable $e) {
            // Index might not exist - that's fine
            if (str_contains($e->getMessage(), 'index_not_found_exception')) {
                $this->line("   No matching indices found");
                return;
            }

            $this->error("   ❌ Error: " . $e->getMessage());
            $this->stats['errors'][] = "Shared index {$type}: " . $e->getMessage();
        }
    }

    /**
     * Display summary of operations.
     */
    protected function displaySummary(bool $isDryRun): void
    {
        $this->info('═══════════════════════════════════════');
        $this->info($isDryRun ? '📊 DRY RUN SUMMARY' : '📊 CLEANUP SUMMARY');
        $this->info('═══════════════════════════════════════');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Indices Deleted', $this->stats['indices_deleted']],
                ['Documents Deleted', $this->stats['documents_deleted']],
                ['Indices Skipped', $this->stats['indices_skipped']],
                ['Errors', count($this->stats['errors'])],
            ]
        );

        if (!empty($this->stats['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($this->stats['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        // Log the operation
        Log::channel('elasticsearch')->info('Elasticsearch indices flush completed', [
            'dry_run' => $isDryRun,
            'stats' => $this->stats
        ]);
    }
}
