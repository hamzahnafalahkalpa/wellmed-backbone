<?php

namespace Projects\WellmedBackbone\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Services\PatientSatuSehatIntegrationService;

/**
 * Migrate old per-patient indices to new single shared index.
 *
 * Old pattern: {prefix}.{tenant}.patient-dashboard-metrics-{patientId}-daily (100k+ indices)
 * New pattern: {prefix}.{tenant}.patient-satu-sehat-integration (1 index per tenant)
 *
 * Usage:
 *   php artisan wellmed-backbone:migrate-patient-integration-indices
 *   php artisan wellmed-backbone:migrate-patient-integration-indices --dry-run
 *   php artisan wellmed-backbone:migrate-patient-integration-indices --tenant=4
 *   php artisan wellmed-backbone:migrate-patient-integration-indices --cleanup-only
 */
class MigratePatientIntegrationIndicesCommand extends Command
{
    protected $signature = 'wellmed-backbone:migrate-patient-integration-indices
                            {--dry-run : Show what would be done without making changes}
                            {--tenant= : Specific tenant ID to migrate}
                            {--prefix= : Override ES prefix (e.g., development, local)}
                            {--cleanup-only : Only delete old indices, skip migration}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Migrate old per-patient ES indices to new shared index pattern';

    protected $client;
    protected PatientSatuSehatIntegrationService $service;

    protected array $stats = [
        'indices_found' => 0,
        'documents_migrated' => 0,
        'indices_deleted' => 0,
        'errors' => [],
    ];

    public function handle(): int
    {
        $this->client = app('elasticsearch');
        $this->service = app(PatientSatuSehatIntegrationService::class);

        $isDryRun = $this->option('dry-run');
        $tenantId = $this->option('tenant');
        $prefixOverride = $this->option('prefix');
        $cleanupOnly = $this->option('cleanup-only');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Patient Integration Indices Migration');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->info('Migration path:');
        $this->line('  OLD: {prefix}.{tenant}.patient-dashboard-metrics-{patientId}-daily');
        $this->line('  NEW: {prefix}.{tenant}.patient-satu-sehat-integration');
        $this->newLine();

        // Confirm before proceeding
        if (!$isDryRun && !$this->option('force')) {
            if (!$this->confirm('This will migrate data and delete old indices. Continue?')) {
                $this->info('Aborted.');
                return Command::SUCCESS;
            }
        }

        // Find old indices
        $oldIndices = $this->findOldIndices($tenantId, $prefixOverride);

        if (empty($oldIndices)) {
            $this->info('✅ No old patient-dashboard-metrics indices found.');
            return Command::SUCCESS;
        }

        $this->stats['indices_found'] = count($oldIndices);
        $this->info("Found {$this->stats['indices_found']} old indices to process");
        $this->newLine();

        // Group by tenant
        $indicesByTenant = $this->groupIndicesByTenant($oldIndices, $prefixOverride);

        foreach ($indicesByTenant as $tid => $indices) {
            $this->processTenant($tid, $indices, $isDryRun, $cleanupOnly);
        }

        $this->newLine();
        $this->displaySummary($isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Find all old patient-dashboard-metrics indices.
     */
    protected function findOldIndices(?string $tenantId, ?string $prefixOverride = null): array
    {
        $prefix = $prefixOverride ?? config('elasticsearch.prefix', 'development');

        $pattern = $tenantId
            ? "{$prefix}.{$tenantId}.patient-dashboard-metrics-*-daily"
            : "{$prefix}.*.patient-dashboard-metrics-*-daily";

        $this->line("   Searching pattern: {$pattern}");

        try {
            $response = $this->client->cat()->indices([
                'index' => $pattern,
                'format' => 'json',
                'h' => 'index,docs.count,store.size'
            ]);

            return $response->asArray();

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'index_not_found_exception')) {
                return [];
            }

            $this->error("Error finding indices: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Group indices by tenant ID.
     */
    protected function groupIndicesByTenant(array $indices, ?string $prefixOverride = null): array
    {
        $grouped = [];
        $prefix = $prefixOverride ?? config('elasticsearch.prefix', 'development');

        foreach ($indices as $index) {
            $indexName = $index['index'];

            // Parse: {prefix}.{tenant}.patient-dashboard-metrics-{patientId}-daily
            // Remove prefix and get tenant
            $withoutPrefix = str_replace("{$prefix}.", '', $indexName);
            $parts = explode('.', $withoutPrefix);

            if (count($parts) >= 2) {
                $tenantId = $parts[0];
                if (!isset($grouped[$tenantId])) {
                    $grouped[$tenantId] = [];
                }
                $grouped[$tenantId][] = $index;
            }
        }

        return $grouped;
    }

    /**
     * Process migration for a single tenant.
     */
    protected function processTenant(string $tenantId, array $indices, bool $isDryRun, bool $cleanupOnly): void
    {
        $count = count($indices);
        $this->info("📦 Processing tenant {$tenantId} ({$count} indices)");

        foreach ($indices as $indexInfo) {
            $indexName = $indexInfo['index'];
            $docsCount = $indexInfo['docs.count'] ?? 0;

            // Extract patient ID from index name
            $patientId = $this->extractPatientId($indexName);

            if (!$patientId) {
                $this->line("   ⚠️  Could not extract patient ID from: {$indexName}");
                continue;
            }

            if (!$cleanupOnly) {
                // Migrate documents
                $this->migrateIndex($indexName, $patientId, (int) $tenantId, $isDryRun);
            }

            // Delete old index
            if (!$isDryRun) {
                try {
                    $this->client->indices()->delete(['index' => $indexName]);
                    $this->stats['indices_deleted']++;
                    $this->line("   🗑️  Deleted: {$indexName}");
                } catch (\Throwable $e) {
                    $this->stats['errors'][] = "Delete {$indexName}: " . $e->getMessage();
                }
            } else {
                $this->line("   [DRY-RUN] Would delete: {$indexName} ({$docsCount} docs)");
            }
        }
    }

    /**
     * Extract patient ID from old index name.
     */
    protected function extractPatientId(string $indexName): ?string
    {
        // Pattern: {prefix}.{tenant}.patient-dashboard-metrics-{patientId}-daily
        if (preg_match('/patient-dashboard-metrics-(.+)-daily$/', $indexName, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Migrate documents from old index to new index.
     */
    protected function migrateIndex(string $oldIndexName, string $patientId, int $tenantId, bool $isDryRun): void
    {
        try {
            // Get the latest document from old index
            $response = $this->client->search([
                'index' => $oldIndexName,
                'body' => [
                    'size' => 1,
                    'sort' => [['timestamp' => ['order' => 'desc']]]
                ]
            ]);

            if (empty($response['hits']['hits'])) {
                $this->line("   📭 No documents in: {$oldIndexName}");
                return;
            }

            $oldDoc = $response['hits']['hits'][0]['_source'];

            if ($isDryRun) {
                $this->line("   [DRY-RUN] Would migrate patient {$patientId}");
                return;
            }

            // Transform old structure to new structure
            $newData = $this->transformDocument($oldDoc);

            // Get workspace_id from old doc
            $workspaceId = $oldDoc['workspace_id'] ?? null;

            // Store in new index using the service
            $result = $this->service->store($patientId, $newData, $tenantId, $workspaceId);

            if ($result['success'] ?? false) {
                $this->stats['documents_migrated']++;
                $this->line("   ✅ Migrated patient: {$patientId}");
            } else {
                $this->stats['errors'][] = "Migrate {$patientId}: " . ($result['error'] ?? 'Unknown error');
            }

        } catch (\Throwable $e) {
            $this->stats['errors'][] = "Migrate {$oldIndexName}: " . $e->getMessage();
        }
    }

    /**
     * Transform old document structure to new structure.
     */
    protected function transformDocument(array $oldDoc): array
    {
        $integrations = $oldDoc['patient_integrations'] ?? [];

        return [
            'general' => [
                'ihs_number' => $integrations['general']['ihs_number'] ?? null,
            ],
            'syncs' => $integrations['syncs'] ?? [],
            'logs' => $integrations['logs'] ?? [],
        ];
    }

    /**
     * Display migration summary.
     */
    protected function displaySummary(bool $isDryRun): void
    {
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info($isDryRun ? '📊 DRY RUN SUMMARY' : '📊 MIGRATION SUMMARY');
        $this->info('═══════════════════════════════════════════════════════════════');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Old Indices Found', $this->stats['indices_found']],
                ['Documents Migrated', $this->stats['documents_migrated']],
                ['Indices Deleted', $this->stats['indices_deleted']],
                ['Errors', count($this->stats['errors'])],
            ]
        );

        if (!empty($this->stats['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach (array_slice($this->stats['errors'], 0, 10) as $error) {
                $this->line("  - {$error}");
            }
            if (count($this->stats['errors']) > 10) {
                $this->line("  ... and " . (count($this->stats['errors']) - 10) . " more errors");
            }
        }

        Log::channel('elasticsearch')->info('Patient integration indices migration completed', [
            'dry_run' => $isDryRun,
            'stats' => $this->stats
        ]);
    }
}
