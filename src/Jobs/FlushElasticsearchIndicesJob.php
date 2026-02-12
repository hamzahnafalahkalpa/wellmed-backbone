<?php

namespace Projects\WellmedBackbone\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Job to flush old Elasticsearch indices based on retention policy.
 *
 * This job is scheduled to run automatically based on config/elasticsearch.php
 * under 'retention.schedule'.
 *
 * Can also be dispatched manually:
 *   FlushElasticsearchIndicesJob::dispatch();
 *   FlushElasticsearchIndicesJob::dispatch('patient-dashboard-metrics');
 *   FlushElasticsearchIndicesJob::dispatch(null, 4); // tenant 4 only
 */
class FlushElasticsearchIndicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600; // 10 minutes max

    protected ?string $indexType;
    protected ?int $tenantId;
    protected ?int $overrideDays;

    /**
     * Create a new job instance.
     *
     * @param string|null $indexType Specific index type to flush, or null for all
     * @param int|null $tenantId Specific tenant ID to flush, or null for all
     * @param int|null $overrideDays Override retention days from config
     */
    public function __construct(
        ?string $indexType = null,
        ?int $tenantId = null,
        ?int $overrideDays = null
    ) {
        $this->indexType = $indexType;
        $this->tenantId = $tenantId;
        $this->overrideDays = $overrideDays;

        // Use configured queue
        $this->onQueue(config('elasticsearch.retention.schedule.queue', 'elasticsearch'));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!config('elasticsearch.retention.enabled', true)) {
            Log::channel('elasticsearch')->info('Elasticsearch retention is disabled, skipping flush job');
            return;
        }

        Log::channel('elasticsearch')->info('Starting scheduled Elasticsearch indices flush', [
            'index_type' => $this->indexType,
            'tenant_id' => $this->tenantId,
            'override_days' => $this->overrideDays,
        ]);

        $arguments = ['--force' => true];

        if ($this->indexType) {
            $arguments['--type'] = $this->indexType;
        }

        if ($this->tenantId) {
            $arguments['--tenant'] = $this->tenantId;
        }

        if ($this->overrideDays) {
            $arguments['--days'] = $this->overrideDays;
        }

        try {
            $exitCode = Artisan::call('wellmed-backbone:flush-elasticsearch-indices', $arguments);

            $output = Artisan::output();

            Log::channel('elasticsearch')->info('Scheduled Elasticsearch indices flush completed', [
                'exit_code' => $exitCode,
                'output_length' => strlen($output),
            ]);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Scheduled Elasticsearch indices flush failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('elasticsearch')->error('FlushElasticsearchIndicesJob permanently failed', [
            'error' => $exception->getMessage(),
            'index_type' => $this->indexType,
            'tenant_id' => $this->tenantId,
        ]);
    }
}
