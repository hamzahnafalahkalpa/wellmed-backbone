<?php

namespace Projects\WellmedBackbone\Services\Concerns;

trait HasStatistic
{
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
     * Only updates count. The previous_count is set when document is created.
     * The transformer calculates change, change_type, percentage_change from count and previous_count.
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

            // Get or create current document (previous_count is set during creation)
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

            // Increment the count
            $currentCount = $currentData['statistics'][$statIndex]['count'] ?? 0;
            $currentData['statistics'][$statIndex]['count'] = $currentCount + $increment;
            $currentData['statistics'][$statIndex]['updated_at'] = now()->toIso8601String();

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            return $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

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
     * Only updates count. The previous_count is set when document is created.
     * The transformer calculates change, change_type, percentage_change from count and previous_count.
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

            // Get or create current document (previous_count is set during creation)
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
            $currentData['statistics'][$statIndex]['updated_at'] = now()->toIso8601String();

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
}