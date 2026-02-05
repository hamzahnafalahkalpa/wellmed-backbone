<?php

namespace Projects\WellmedBackbone\Services\Concerns;
use Illuminate\Support\Str;

trait HasStatistic{
    /**
     * Increment new patient count when a patient is created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewPatient(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();

        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementStatistic('new_patients', $periodType, $tenantId, $workspaceId);
        }            
        return $results;
    }

    /**
     * Update total patient count.
     * Should be called after a patient is created to update the total count.
     *
     * @param int $totalCount Current total patient count
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function updateTotalPatients(int $totalCount, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {            
            $results[$periodType] = $this->updateStatisticCount('patients', $totalCount, $periodType, $tenantId, $workspaceId);
        }

        return $results;
    }

    /**
     * Increment new treatment at sign off.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewTreatment(int $count,?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementStatistic('treatment', $periodType, $tenantId, $workspaceId, $count);
        }
        return $results;
    }

    /**
     * Increment a specific statistic counter.
     *
     * @param string $statisticKey The statistic key (e.g., 'new_patients', 'patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementStatistic(string $statisticKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Map key to ID (e.g., 'new_patients' -> 'new-patients')
            $statisticId = $this->mapStatisticKeyToId($statisticKey);

            // Find the statistic in the array
            $statIndex = $this->findStatisticIndex($currentData['statistics'], $statisticId);
            if ($statIndex === null) {
                return [
                    'success' => false,
                    'error' => "Statistic '{$statisticId}' not found"
                ];
            }
            // Increment the statistic
            $currentCount = $currentData['statistics'][$statIndex]['count'] ?? 0;
            $currentData['statistics'][$statIndex]['count'] = $currentCount + $increment;

            // Calculate change from previous period
            $previousData = $this->getPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);
            $previousStatIndex = $this->findStatisticIndex($previousData['statistics'], $statisticId);
            $previousCount = $previousStatIndex !== null
                ? ($previousData['statistics'][$previousStatIndex]['count'] ?? 0)
                : 0;

            $change = $currentData['statistics'][$statIndex]['count'] - $previousCount;
            $currentData['statistics'][$statIndex]['change'] = abs($change);
            $currentData['statistics'][$statIndex]['change_type'] = $change >= 0 ? 'increase' : 'decrease';
            $currentData['statistics'][$statIndex]['percentage_change'] = $previousCount > 0
                ? round((abs($change) / $previousCount) * 100, 1)
                : 100;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);
            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment statistic', [
                'error' => $e->getMessage(),
                'statistic' => $statisticKey,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update a specific statistic count (set absolute value).
     *
     * @param string $statisticKey
     * @param int $count
     * @param string $periodType
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array
     */
    public function updateStatisticCount(string $statisticKey, int $count, string $periodType, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Map key to ID (e.g., 'new_patients' -> 'new-patients')
            $statisticId = $this->mapStatisticKeyToId($statisticKey);

            // Find the statistic in the array
            $statIndex = $this->findStatisticIndex($currentData['statistics'], $statisticId);

            if ($statIndex === null) {
                return [
                    'success' => false,
                    'error' => "Statistic '{$statisticId}' not found"
                ];
            }

            // Set the count
            $currentData['statistics'][$statIndex]['count'] = $count;

            // Calculate change from previous period
            $previousData = $this->getPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);
            $previousStatIndex = $this->findStatisticIndex($previousData['statistics'], $statisticId);
            $previousCount = $previousStatIndex !== null
                ? ($previousData['statistics'][$previousStatIndex]['count'] ?? 0)
                : 0;

            $change = $count - $previousCount;
            $currentData['statistics'][$statIndex]['change'] = abs($change);
            $currentData['statistics'][$statIndex]['change_type'] = $change >= 0 ? 'increase' : 'decrease';
            $currentData['statistics'][$statIndex]['percentage_change'] = $previousCount > 0
                ? round((abs($change) / $previousCount) * 100, 1)
                : 0;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();
            // Store updated document
            return $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to update statistic count', [
                'error' => $e->getMessage(),
                'statistic' => $statisticKey,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Find statistic index by ID in statistics array.
     *
     * @param array $statistics
     * @param string $statisticId
     * @return int|null
     */
    protected function findStatisticIndex(array $statistics, string $statisticId): ?int
    {
        foreach ($statistics as $index => $stat) {
            if (($stat['id'] ?? '') === $statisticId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Map statistic key to ID (for backward compatibility).
     *
     * @param string $key
     * @return string
     */
    protected function mapStatisticKeyToId(string $key): string
    {
        return match ($key) {
            'new_patients' => 'new-patients',
            default => $key
        };
    }

    /**
     * Get default statistics array with frontend presentation structure.
     *
     * @param string $periodType
     * @return array
     */
    protected function getDefaultStatistics(string $periodType,? array $data = []): array
    {
        $changeLabel = $this->getChangeLabel($periodType);
        $response = [
            [
                'id' => 'patients',
                'label' => 'Jumlah Pasien',
                'count' => 0,
                'change' => 0,
                'change_type' => 'increase',
                'percentage_change' => 0,
                'change_label' => $changeLabel,
                'icon' => 'mdi:account-group',
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-cyan-400',
                'bg_light' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'border_color' => 'border-blue-200'
            ],
            [
                'id' => 'new-patients',
                'label' => 'Pasien Baru',
                'count' => 0,
                'change' => 0,
                'change_type' => 'increase',
                'percentage_change' => 0,
                'change_label' => $changeLabel,
                'icon' => 'mdi:account-plus',
                'color' => 'purple',
                'gradient' => 'from-purple-500 to-pink-400',
                'bg_light' => 'bg-purple-50',
                'text_color' => 'text-purple-600',
                'border_color' => 'border-purple-200'
            ],
            [
                'id' => 'revenue',
                'label' => 'Omzet',
                'count' => 0,
                'change' => 0,
                'change_type' => 'increase',
                'percentage_change' => 0,
                'change_label' => $changeLabel,
                'icon' => 'mdi:cash-multiple',
                'color' => 'emerald',
                'gradient' => 'from-emerald-500 to-teal-400',
                'bg_light' => 'bg-emerald-50',
                'text_color' => 'text-emerald-600',
                'border_color' => 'border-emerald-200',
                'is_currency' => true
            ],
            [
                'id' => 'treatment',
                'label' => 'Tindakan Dipesankan',
                'count' => 0,
                'change' => 0,
                'change_type' => 'increase',
                'percentage_change' => 0,
                'change_label' => $changeLabel,
                'icon' => 'mdi:clipboard-list',
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-400',
                'bg_light' => 'bg-orange-50',
                'text_color' => 'text-orange-600',
                'border_color' => 'border-orange-200'
            ]
        ];
        if (count($data) > 0){
            foreach ($response as &$resp){
                $id = Str::snake($resp['id']);
                if (isset($data[$id])){
                    foreach ($data[$id] as $key => $data_item) {
                        $resp[$key] = $data_item;
                    }
                }
            }
        }
        return $response;
    }
}