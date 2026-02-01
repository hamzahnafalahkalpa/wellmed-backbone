<?php

namespace Projects\WellmedBackbone\Commands;

use Illuminate\Console\Command;
use Projects\WellmedBackbone\Services\DashboardMetricsService;
use Carbon\Carbon;

class TestDashboardMetricsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:metrics:test
                            {action=store : Action to perform (store, get, range, aggregate, delete)}
                            {--period=daily : Period type (daily, monthly, yearly)}
                            {--date= : Specific date (YYYY-MM-DD)}
                            {--start-date= : Start date for range (YYYY-MM-DD)}
                            {--end-date= : End date for range (YYYY-MM-DD)}
                            {--metric=patients : Metric name for aggregation}
                            {--agg=sum : Aggregation type (sum, avg, min, max)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Dashboard Metrics Elasticsearch integration';

    protected DashboardMetricsService $metricsService;

    /**
     * Create a new command instance.
     */
    public function __construct(DashboardMetricsService $metricsService)
    {
        parent::__construct();
        $this->metricsService = $metricsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        $this->info("Dashboard Metrics Test - Action: {$action}");
        $this->newLine();

        try {
            match($action) {
                'store' => $this->testStore(),
                'get' => $this->testGet(),
                'range' => $this->testRange(),
                'aggregate' => $this->testAggregate(),
                'delete' => $this->testDelete(),
                default => $this->error("Unknown action: {$action}")
            };
        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Test storing metrics
     */
    protected function testStore()
    {
        $this->info("Testing store operation...");

        $period = $this->option('period');
        $sampleData = include base_path('elasticsearch/examples/sample-dashboard-data.php');

        $data = $sampleData[$period] ?? $sampleData['daily'];

        $this->info("Storing {$period} metrics...");

        $result = $this->metricsService->store($data, $period);

        if ($result['success']) {
            $this->info("✓ Metrics stored successfully!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $result['id']],
                    ['Index', $result['index']],
                    ['Period', $period]
                ]
            );
        } else {
            $this->error("✗ Failed to store metrics: " . $result['error']);
        }
    }

    /**
     * Test retrieving metrics
     */
    protected function testGet()
    {
        $this->info("Testing get operation...");

        $period = $this->option('period');
        $filters = [];

        if ($date = $this->option('date')) {
            $filters['date'] = $date;
        }

        $this->info("Retrieving {$period} metrics...");

        $result = $this->metricsService->get($period, $filters);

        if ($result['success']) {
            $this->info("✓ Metrics retrieved successfully!");

            if ($result['data']) {
                $data = $result['data'];
                $this->newLine();
                $this->info("Statistics:");
                $this->table(
                    ['Metric', 'Count', 'Change', 'Type'],
                    [
                        ['Patients', $data['statistics']['patients']['count'], $data['statistics']['patients']['change'], $data['statistics']['patients']['change_type']],
                        ['New Patients', $data['statistics']['new_patients']['count'], $data['statistics']['new_patients']['change'], $data['statistics']['new_patients']['change_type']],
                        ['Revenue', number_format($data['statistics']['revenue']['count']), number_format($data['statistics']['revenue']['change']), $data['statistics']['revenue']['change_type']],
                        ['Unfinished', $data['statistics']['unfinished']['count'], $data['statistics']['unfinished']['change'], $data['statistics']['unfinished']['change_type']],
                    ]
                );

                $this->newLine();
                $this->info("Pending Items:");
                $this->table(
                    ['Item', 'Count'],
                    [
                        ['Unsigned Visits', $data['pending_items']['unsigned_visits']],
                        ['Unsynced Patients', $data['pending_items']['unsynced_patients']],
                        ['Incomplete Diagnosis', $data['pending_items']['incomplete_diagnosis']],
                    ]
                );

                if (!empty($data['queue_services'])) {
                    $this->newLine();
                    $this->info("Queue Services:");
                    $queueData = array_map(function($service) {
                        return [
                            $service['service_name'],
                            $service['current_queue'],
                            $service['waiting'],
                            $service['serving']
                        ];
                    }, $data['queue_services']);

                    $this->table(['Service', 'Total Queue', 'Waiting', 'Serving'], $queueData);
                }
            } else {
                $this->warn("No data found");
            }
        } else {
            $this->error("✗ Failed to retrieve metrics: " . $result['error']);
        }
    }

    /**
     * Test range query
     */
    protected function testRange()
    {
        $this->info("Testing range operation...");

        $period = $this->option('period');
        $startDate = Carbon::parse($this->option('start-date') ?? now()->subDays(7));
        $endDate = Carbon::parse($this->option('end-date') ?? now());

        $this->info("Retrieving {$period} metrics from {$startDate->toDateString()} to {$endDate->toDateString()}...");

        $result = $this->metricsService->getRange($period, $startDate, $endDate);

        if ($result['success']) {
            $this->info("✓ Retrieved {$result['total']} records");

            if (!empty($result['data'])) {
                $tableData = array_map(function($item) {
                    return [
                        $item['date'],
                        $item['statistics']['patients']['count'],
                        $item['statistics']['new_patients']['count'],
                        number_format($item['statistics']['revenue']['count'])
                    ];
                }, array_slice($result['data'], 0, 10)); // Show first 10

                $this->table(['Date', 'Patients', 'New Patients', 'Revenue'], $tableData);

                if ($result['total'] > 10) {
                    $this->info("... and " . ($result['total'] - 10) . " more records");
                }
            }
        } else {
            $this->error("✗ Failed to retrieve range: " . $result['error']);
        }
    }

    /**
     * Test aggregation
     */
    protected function testAggregate()
    {
        $this->info("Testing aggregate operation...");

        $period = $this->option('period');
        $metric = $this->option('metric');
        $aggregation = $this->option('agg');

        $filters = [];
        if ($date = $this->option('date')) {
            $filters['date'] = $date;
        }

        $this->info("Aggregating {$metric} using {$aggregation}...");

        $result = $this->metricsService->aggregate($period, $metric, $aggregation, $filters);

        if ($result['success']) {
            $this->info("✓ Aggregation completed!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Metric', $metric],
                    ['Aggregation', $aggregation],
                    ['Result', $metric === 'revenue' ? 'Rp ' . number_format($result['value']) : $result['value']],
                    ['Documents', $result['total_documents']]
                ]
            );
        } else {
            $this->error("✗ Failed to aggregate: " . $result['error']);
        }
    }

    /**
     * Test delete operation
     */
    protected function testDelete()
    {
        $this->info("Testing delete operation...");

        if (!$this->confirm('Are you sure you want to delete metrics? This action cannot be undone.')) {
            $this->info('Delete operation cancelled.');
            return;
        }

        $period = $this->option('period');
        $filters = [];

        if ($date = $this->option('date')) {
            $filters['date'] = $date;
            $this->warn("Deleting {$period} metrics for date: {$date}");
        } else {
            $this->warn("No date filter specified. This will delete ALL {$period} metrics!");
            if (!$this->confirm('Continue?')) {
                $this->info('Delete operation cancelled.');
                return;
            }
        }

        $result = $this->metricsService->delete($period, $filters);

        if ($result['success']) {
            $this->info("✓ Deleted {$result['deleted']} documents");
        } else {
            $this->error("✗ Failed to delete: " . $result['error']);
        }
    }
}
