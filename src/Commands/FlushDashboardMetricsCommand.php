<?php

namespace Projects\WellmedBackbone\Commands;

use Carbon\Carbon;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Services\DashboardMetricsService;

class FlushDashboardMetricsCommand extends Command
{
    protected $signature = 'dashboard:flush
                            {--period=daily : Period type: daily, weekly, monthly, yearly}
                            {--date= : Specific date (YYYY-MM-DD), defaults to today}
                            {--tenant_id= : Tenant ID for multi-tenant context}
                            {--workspace_id= : Workspace ID}
                            {--dry-run : Preview data without indexing}
                            {--delete-indices : Delete all dashboard indices for the tenant}
                            {--generate-default : Generate default document for the specified date}
                            {--with-random-data : Populate document with random test data}';

    protected $description = 'Aggregate and flush dashboard metrics to Elasticsearch';

    protected DashboardMetricsService $service;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->service = app(DashboardMetricsService::class);

        $period = $this->option('period');
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $tenantId = $this->option('tenant_id');
        if (isset($tenantId)){
            $tenant_model = app(config('database.models.Tenant'))->findOrFail($tenantId);
            $workspaceId = $tenant_model->reference_id;
        }
        if (!isset($workspaceId)){
            $workspaceId = $this->option('workspace_id');
            if (!isset($tenantId)){
                $workspace_model = app('database.models.Workspace')->with('tenant')->findOrFail($workspaceId);
                $tenantId = $workspace_model->tenant->getKey();
            }
        }
        if (isset($tenantId)){
            MicroTenant::tenantImpersonate($tenantId);
        }
        $dryRun = $this->option('dry-run');
        $deleteIndices = $this->option('delete-indices');
        $generateDefault = $this->option('generate-default');

        if (!$tenantId || !$workspaceId) {
            $this->error('Both --tenant_id and --workspace_id options are required');
            return Command::FAILURE;
        }

        $tenantId = (int) $tenantId;

        // Handle delete indices option
        if ($deleteIndices) {
            return $this->handleDeleteIndices($tenantId);
        }

        // Handle generate default document option
        if ($generateDefault) {
            return $this->handleGenerateDefault($period, $date, $tenantId, $workspaceId);
        }

        $this->info("Aggregating dashboard metrics for {$period} on {$date->format('Y-m-d')}");
        $this->info("   Tenant ID: {$tenantId}");
        $this->info("   Workspace ID: {$workspaceId}");

        try {
            // Aggregate data from database
            $data = $this->aggregateDashboardData($date, $period, $tenantId, $workspaceId);

            if ($dryRun) {
                $this->info("\nDry run - Preview of data to be indexed:");
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            $results = [];

            // Update total patients count
            $patientCount = $data['statistics']['patients'] ?? 0;
            if ($patientCount > 0) {
                $results['patients'] = $this->service->updateTotalPatients($patientCount, $tenantId, $workspaceId);
                $this->info("   Updated total patients: {$patientCount}");
            }

            // Check if any updates succeeded
            $anySuccess = false;
            foreach ($results as $key => $result) {
                if (isset($result[$period]['success']) && $result[$period]['success']) {
                    $anySuccess = true;
                }
            }

            if ($anySuccess || empty($results)) {
                $this->info("[OK] Dashboard metrics successfully updated!");
                return Command::SUCCESS;
            } else {
                $this->error("[FAIL] Failed to update dashboard metrics");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("[FAIL] Error: {$e->getMessage()}");
            Log::error('Dashboard flush command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Aggregate dashboard data from database
     *
     * @param Carbon $date
     * @param string $period
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    protected function aggregateDashboardData(
        Carbon $date,
        string $period,
        ?int $tenantId = null,
        mixed $workspaceId = null
    ): array {
        $this->warn("Using database aggregation - implement actual queries for production");

        $patientCount = $this->getPatientCount($date, $tenantId, $workspaceId);

        return [
            'statistics' => [
                'patients' => $patientCount,
                'new_patients' => 0, // TODO: implement actual query
                'revenue' => 0,      // TODO: implement actual query
                'treatment' => 0,    // TODO: implement actual query
            ],
            'pending_items' => $this->getPendingItems($tenantId, $workspaceId),
            'queue_services' => $this->getQueueServices($date, $tenantId, $workspaceId),
            'diagnosis_treatment' => $this->getDiagnosisTreatment($date, $tenantId, $workspaceId),
        ];
    }

    /**
     * Get patient count for specific date
     */
    protected function getPatientCount(Carbon $date, ?int $tenantId = null, mixed $workspaceId = null): int
    {
        // TODO: Implement actual query
        // Example:
        // return DB::table('visits')
        //     ->whereDate('visit_date', $date)
        //     ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        //     ->when($workspaceId, fn($q) => $q->where('workspace_id', $workspaceId))
        //     ->count();

        return 0;
    }

    /**
     * Get pending items
     */
    protected function getPendingItems(?int $tenantId = null, mixed $workspaceId = null): array
    {
        // TODO: Implement actual queries for pending items
        return [];
    }

    /**
     * Get queue services
     */
    protected function getQueueServices(Carbon $date, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        // TODO: Implement actual queries for queue services
        return [];
    }

    /**
     * Get diagnosis and treatment records
     */
    protected function getDiagnosisTreatment(Carbon $date, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        // TODO: Implement actual queries for diagnosis/treatment
        return [];
    }

    /**
     * Handle generation of default document for specific date
     *
     * @param string $period
     * @param Carbon $date
     * @param int $tenantId
     * @param mixed $workspaceId
     * @return int
     */
    protected function handleGenerateDefault(string $period, Carbon $date, int $tenantId, mixed $workspaceId): int
    {
        try {
            $withRandomData = $this->option('with-random-data');

            $this->info("Generating default dashboard document for {$date->format('Y-m-d')}");
            $this->info("   Period: {$period}");
            $this->info("   Tenant ID: {$tenantId}");
            $this->info("   Workspace ID: {$workspaceId}");
            if ($withRandomData) {
                $this->info("   With Random Data: Yes");
            }

            if ($withRandomData) {
                // Generate document with random data
                $result = $this->service->generateDocumentWithRandomData($period, $tenantId, $workspaceId, $date);
            } else {
                // Create default document
                $result = $this->service->generateDefaultDocument($period, $tenantId, $workspaceId, $date);
            }

            if ($result['success'] ?? false) {
                if ($result['already_exists'] ?? false) {
                    $this->warn("   Document already exists for this date.");
                } else {
                    $this->info("   Successfully created document.");
                    $this->info("   Document ID: " . ($result['id'] ?? 'N/A'));
                    if ($withRandomData) {
                        $this->info("   Populated with random test data.");
                    }
                }
                return Command::SUCCESS;
            } else {
                $this->error("   Failed to create document: " . ($result['error'] ?? 'Unknown error'));
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("[FAIL] Error generating default document: {$e->getMessage()}");
            Log::error('Failed to generate default dashboard document', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'date' => $date->format('Y-m-d'),
                'tenant_id' => $tenantId,
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Handle deletion of all dashboard indices with specific pattern
     *
     * @param int $tenantId
     * @return int
     */
    protected function handleDeleteIndices(int $tenantId): int
    {
        try {
            $client = app('elasticsearch');
            $prefix = config('elasticsearch.prefix', 'development');
            $separator = config('elasticsearch.separator', '.');

            // Pattern: {prefix}.{tenant_id}.dashboard-*
            $pattern = "{$prefix}{$separator}dashboard-*";

            $this->info("Searching for indices matching pattern: {$pattern}");

            // Get all indices matching the pattern
            $response = $client->cat()->indices([
                'index' => $pattern,
                'format' => 'json'
            ]);

            // Convert response to array
            $indices = $response->asArray();

            if (empty($indices)) {
                $this->warn("No indices found matching pattern: {$pattern}");
                return Command::SUCCESS;
            }

            $indexNames = array_column($indices, 'index');
            $this->info("Found " . count($indexNames) . " indices to delete:");

            foreach ($indexNames as $indexName) {
                $this->line("  - {$indexName}");
            }

            // Confirm deletion
            if (!$this->confirm("Are you sure you want to delete these " . count($indexNames) . " indices?", false)) {
                $this->info("Deletion cancelled.");
                return Command::SUCCESS;
            }

            // Delete indices
            $deletedCount = 0;
            foreach ($indexNames as $indexName) {
                try {
                    $client->indices()->delete(['index' => $indexName]);
                    $this->info("   Deleted: {$indexName}");
                    $deletedCount++;
                } catch (\Exception $e) {
                    $this->error("   Failed to delete {$indexName}: " . $e->getMessage());
                }
            }

            $this->info("\n[OK] Successfully deleted {$deletedCount} out of " . count($indexNames) . " indices.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("[FAIL] Error deleting indices: {$e->getMessage()}");
            Log::error('Failed to delete dashboard indices', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenantId,
            ]);
            return Command::FAILURE;
        }
    }
}
