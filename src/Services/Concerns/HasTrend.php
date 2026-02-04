<?php

namespace Projects\WellmedBackbone\Services\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HasTrend
{
    /**
     * Increment trend count when a visit registration is created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param array $data Array containing medic service info ['service' => name, 'service_label' => label]
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementTrend(array $data, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementTrendForPeriod($periodType, $data, $tenantId, $workspaceId);
        }

        return $results;
    }

    /**
     * Increment trend for a specific period type.
     *
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param array $data Array containing medic service info
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementTrendForPeriod(string $periodType, array $data, ?int $tenantId = null, mixed $workspaceId = null, int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Ensure trends structure exists with proper initialization
            if (!isset($currentData['trends']) || !is_array($currentData['trends'])) {
                $currentData['trends'] = $this->getDefaultTrends($periodType, $timestamp);
            }

            // Ensure services array exists
            if (!isset($currentData['trends']['services']) || !is_array($currentData['trends']['services'])) {
                $currentData['trends']['services'] = [];
            }

            // Find or create medic service entry
            $serviceLabel = $data['service_label'] ?? $data['service'] ?? 'Unknown';
            $serviceName = $data['service'] ?? $serviceLabel;

            // Normalize service label for consistent matching (trim and uppercase for comparison)
            $normalizedLabel = trim($serviceLabel);

            // Find the service index in trends data
            $serviceIndex = $this->findTrendServiceIndex($currentData['trends'], $normalizedLabel);

            if ($serviceIndex === null) {
                // Add new service to trends with fresh data array
                $currentData['trends']['services'][] = [
                    'service' => $serviceName,
                    'service_label' => $normalizedLabel,
                    'data' => $this->initializeTrendDataArray($periodType, $timestamp)
                ];
                $serviceIndex = count($currentData['trends']['services']) - 1;
            } else {
                // Ensure existing service has proper data structure
                $currentData['trends']['services'][$serviceIndex] = $this->ensureServiceDataIntegrity(
                    $currentData['trends']['services'][$serviceIndex],
                    $periodType,
                    $timestamp
                );
            }

            // Increment the count for the current period with bounds checking
            $periodIndex = $this->getTrendPeriodIndex($periodType, $timestamp);
            $periodCount = $this->getTrendPeriodCount($periodType);

            // Ensure period index is within bounds
            if ($periodIndex >= 0 && $periodIndex < $periodCount) {
                if (isset($currentData['trends']['services'][$serviceIndex]['data'][$periodIndex])) {
                    $currentData['trends']['services'][$serviceIndex]['data'][$periodIndex]['count'] += $increment;
                }
            }

            // Update the dataset source for ECharts format
            $currentData['trends']['dataset'] = $this->buildTrendDataset($currentData['trends'], $periodType, $timestamp);

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            return $result;

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment trend', [
                'error' => $e->getMessage(),
                'service' => $data['service'] ?? 'unknown',
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Ensure service data has proper structure and all period buckets.
     *
     * @param array $service
     * @param string $periodType
     * @param Carbon $timestamp
     * @return array
     */
    protected function ensureServiceDataIntegrity(array $service, string $periodType, Carbon $timestamp): array
    {
        $periodCount = $this->getTrendPeriodCount($periodType);

        // If data is missing or incomplete, reinitialize while preserving existing counts
        if (!isset($service['data']) || !is_array($service['data']) || count($service['data']) !== $periodCount) {
            $freshData = $this->initializeTrendDataArray($periodType, $timestamp);

            // Preserve existing counts if data exists
            if (isset($service['data']) && is_array($service['data'])) {
                foreach ($service['data'] as $index => $dataPoint) {
                    if (isset($freshData[$index]) && isset($dataPoint['count'])) {
                        $freshData[$index]['count'] = $dataPoint['count'];
                    }
                }
            }

            $service['data'] = $freshData;
        }

        return $service;
    }

    /**
     * Get trend data for display.
     *
     * @param string $periodType
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function getTrends(string $periodType, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['trends']) || empty($currentData['trends'])) {
                return $this->getDefaultTrends($periodType, $timestamp);
            }

            // Ensure dataset is up to date
            $currentData['trends']['dataset'] = $this->buildTrendDataset($currentData['trends'], $periodType, $timestamp);

            return $currentData['trends'];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get trends', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);

            return $this->getDefaultTrends($periodType, now());
        }
    }

    /**
     * Get default trends structure.
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return array
     */
    protected function getDefaultTrends(string $periodType, Carbon $timestamp): array
    {
        return [
            'services' => [],
            'dataset' => [
                'source' => [
                    $this->getTrendLabels($periodType, $timestamp)
                ]
            ],
            'title' => 'Tren Kunjungan per Poliklinik',
            'subtitle' => $this->getTrendSubtitle($periodType),
            'chart_type' => 'line',
            'series_layout' => 'row',
            'period_type' => $periodType
        ];
    }

    /**
     * Get period key for the current timestamp.
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return string
     */
    protected function getTrendPeriodKey(string $periodType, Carbon $timestamp): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => $timestamp->format('Y-m-d'),
            self::PERIOD_WEEKLY => $timestamp->format('Y') . '-W' . $timestamp->format('W'),
            self::PERIOD_MONTHLY => $timestamp->format('Y-m'),
            self::PERIOD_YEARLY => $timestamp->format('Y'),
            default => $timestamp->format('Y-m-d')
        };
    }

    /**
     * Get period index for incrementing the correct bucket.
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return int
     */
    protected function getTrendPeriodIndex(string $periodType, Carbon $timestamp): int
    {
        $now = Carbon::now();

        return match ($periodType) {
            self::PERIOD_DAILY => 6 - $now->diffInDays($timestamp->copy()->startOfDay()),
            self::PERIOD_WEEKLY => 3 - $now->diffInWeeks($timestamp->copy()->startOfWeek()),
            self::PERIOD_MONTHLY => 11 - $now->diffInMonths($timestamp->copy()->startOfMonth()),
            self::PERIOD_YEARLY => 4 - $now->diffInYears($timestamp->copy()->startOfYear()),
            default => 0
        };
    }

    /**
     * Initialize trend data array with period buckets.
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return array
     */
    protected function initializeTrendDataArray(string $periodType, Carbon $timestamp): array
    {
        $data = [];
        $count = $this->getTrendPeriodCount($periodType);
        $now = Carbon::now();

        for ($i = $count - 1; $i >= 0; $i--) {
            $periodTimestamp = match ($periodType) {
                self::PERIOD_DAILY => $now->copy()->subDays($i),
                self::PERIOD_WEEKLY => $now->copy()->subWeeks($i),
                self::PERIOD_MONTHLY => $now->copy()->subMonths($i),
                self::PERIOD_YEARLY => $now->copy()->subYears($i),
                default => $now->copy()->subDays($i)
            };

            $data[] = [
                'key' => $this->getTrendPeriodKey($periodType, $periodTimestamp),
                'label' => $this->getTrendPeriodLabel($periodType, $periodTimestamp),
                'count' => 0
            ];
        }

        return $data;
    }

    /**
     * Get the number of periods to show for each period type.
     *
     * @param string $periodType
     * @return int
     */
    protected function getTrendPeriodCount(string $periodType): int
    {
        return match ($periodType) {
            self::PERIOD_DAILY => 7,    // 7 days
            self::PERIOD_WEEKLY => 4,   // 4 weeks
            self::PERIOD_MONTHLY => 12, // 12 months
            self::PERIOD_YEARLY => 5,   // 5 years
            default => 7
        };
    }

    /**
     * Get x-axis labels for the trend chart.
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return array
     */
    protected function getTrendLabels(string $periodType, Carbon $timestamp): array
    {
        $labels = ['Kunjungan']; // First element is the header
        $count = $this->getTrendPeriodCount($periodType);
        $now = Carbon::now();

        for ($i = $count - 1; $i >= 0; $i--) {
            $periodTimestamp = match ($periodType) {
                self::PERIOD_DAILY => $now->copy()->subDays($i),
                self::PERIOD_WEEKLY => $now->copy()->subWeeks($i),
                self::PERIOD_MONTHLY => $now->copy()->subMonths($i),
                self::PERIOD_YEARLY => $now->copy()->subYears($i),
                default => $now->copy()->subDays($i)
            };

            $labels[] = $this->getTrendPeriodLabel($periodType, $periodTimestamp);
        }

        return $labels;
    }

    /**
     * Get label for a specific period.
     *
     * @param string $periodType
     * @param Carbon $timestamp
     * @return string
     */
    protected function getTrendPeriodLabel(string $periodType, Carbon $timestamp): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => $timestamp->format('d M'),
            self::PERIOD_WEEKLY => 'W' . $timestamp->format('W'),
            self::PERIOD_MONTHLY => $timestamp->format('M Y'),
            self::PERIOD_YEARLY => $timestamp->format('Y'),
            default => $timestamp->format('d M')
        };
    }

    /**
     * Get subtitle for trend chart.
     *
     * @param string $periodType
     * @return string
     */
    protected function getTrendSubtitle(string $periodType): string
    {
        return match ($periodType) {
            self::PERIOD_DAILY => 'Berdasarkan 7 hari terakhir',
            self::PERIOD_WEEKLY => 'Berdasarkan 4 minggu terakhir',
            self::PERIOD_MONTHLY => 'Berdasarkan 12 bulan terakhir',
            self::PERIOD_YEARLY => 'Berdasarkan 5 tahun terakhir',
            default => ''
        };
    }

    /**
     * Find service index in trends data.
     * Uses normalized comparison (trimmed) to ensure consistent matching.
     *
     * @param array $trends
     * @param string $serviceLabel
     * @return int|null
     */
    protected function findTrendServiceIndex(array $trends, string $serviceLabel): ?int
    {
        if (!isset($trends['services']) || !is_array($trends['services'])) {
            return null;
        }

        $normalizedSearch = trim($serviceLabel);

        foreach ($trends['services'] as $index => $service) {
            $existingLabel = trim($service['service_label'] ?? '');
            if ($existingLabel === $normalizedSearch) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Get all services currently tracked in trends.
     *
     * @param string $periodType
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function getTrendServices(string $periodType, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            if (!isset($currentData['trends']['services']) || !is_array($currentData['trends']['services'])) {
                return [];
            }

            return array_map(function ($service) {
                return [
                    'service' => $service['service'] ?? 'Unknown',
                    'service_label' => $service['service_label'] ?? 'Unknown',
                    'total_count' => $this->calculateServiceTotalCount($service)
                ];
            }, $currentData['trends']['services']);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get trend services', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);

            return [];
        }
    }

    /**
     * Calculate total count for a service across all periods.
     *
     * @param array $service
     * @return int
     */
    protected function calculateServiceTotalCount(array $service): int
    {
        if (!isset($service['data']) || !is_array($service['data'])) {
            return 0;
        }

        $total = 0;
        foreach ($service['data'] as $dataPoint) {
            $total += $dataPoint['count'] ?? 0;
        }

        return $total;
    }

    /**
     * Build ECharts dataset from trends data.
     * Services are sorted by total visits (most active first).
     *
     * @param array $trends
     * @param string $periodType
     * @param Carbon $timestamp
     * @return array
     */
    protected function buildTrendDataset(array $trends, string $periodType, Carbon $timestamp): array
    {
        $source = [];

        // First row is labels
        $source[] = $this->getTrendLabels($periodType, $timestamp);

        // Add each service as a row
        if (isset($trends['services']) && is_array($trends['services'])) {
            // Sort services by total count (most visits first)
            $services = $trends['services'];
            usort($services, function ($a, $b) {
                $totalA = $this->calculateServiceTotalCount($a);
                $totalB = $this->calculateServiceTotalCount($b);
                return $totalB <=> $totalA; // Descending order
            });

            $periodCount = $this->getTrendPeriodCount($periodType);

            foreach ($services as $service) {
                $row = [$service['service_label'] ?? $service['service'] ?? 'Unknown'];

                if (isset($service['data']) && is_array($service['data'])) {
                    // Ensure we have exactly the right number of data points
                    for ($i = 0; $i < $periodCount; $i++) {
                        $row[] = $service['data'][$i]['count'] ?? 0;
                    }
                } else {
                    // Fill with zeros if no data
                    for ($i = 0; $i < $periodCount; $i++) {
                        $row[] = 0;
                    }
                }

                $source[] = $row;
            }
        }

        return ['source' => $source];
    }

    /**
     * Get aggregated trend data across multiple periods for historical view.
     *
     * @param string $periodType
     * @param int $periodsBack Number of periods to look back
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function getHistoricalTrends(string $periodType, int $periodsBack, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $now = Carbon::now();

            $aggregatedServices = [];
            $labels = ['Kunjungan'];

            for ($i = $periodsBack - 1; $i >= 0; $i--) {
                $periodTimestamp = match ($periodType) {
                    self::PERIOD_DAILY => $now->copy()->subDays($i),
                    self::PERIOD_WEEKLY => $now->copy()->subWeeks($i),
                    self::PERIOD_MONTHLY => $now->copy()->subMonths($i),
                    self::PERIOD_YEARLY => $now->copy()->subYears($i),
                    default => $now->copy()->subDays($i)
                };

                $labels[] = $this->getTrendPeriodLabel($periodType, $periodTimestamp);

                // Get data for this period
                $periodData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $periodTimestamp);

                if (isset($periodData['trends']['services'])) {
                    foreach ($periodData['trends']['services'] as $service) {
                        $serviceLabel = $service['service_label'] ?? 'Unknown';

                        if (!isset($aggregatedServices[$serviceLabel])) {
                            $aggregatedServices[$serviceLabel] = [
                                'service' => $service['service'] ?? $serviceLabel,
                                'service_label' => $serviceLabel,
                                'counts' => array_fill(0, $periodsBack, 0)
                            ];
                        }

                        // Sum up the counts for this service in this period
                        $periodIndex = $periodsBack - 1 - $i;
                        $totalCount = 0;
                        if (isset($service['data']) && is_array($service['data'])) {
                            foreach ($service['data'] as $dataPoint) {
                                $totalCount += $dataPoint['count'] ?? 0;
                            }
                        }
                        $aggregatedServices[$serviceLabel]['counts'][$periodIndex] = $totalCount;
                    }
                }
            }

            // Build dataset
            $source = [$labels];
            foreach ($aggregatedServices as $service) {
                $row = [$service['service_label']];
                foreach ($service['counts'] as $count) {
                    $row[] = $count;
                }
                $source[] = $row;
            }

            return [
                'services' => array_values($aggregatedServices),
                'dataset' => ['source' => $source],
                'title' => 'Tren Kunjungan per Poliklinik',
                'subtitle' => $this->getTrendSubtitle($periodType),
                'chart_type' => 'line',
                'series_layout' => 'row',
                'period_type' => $periodType
            ];

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to get historical trends', [
                'error' => $e->getMessage(),
                'period_type' => $periodType
            ]);

            return $this->getDefaultTrends($periodType, now());
        }
    }
}
