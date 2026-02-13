<?php

namespace Projects\WellmedBackbone\Services\Concerns;

use Illuminate\Support\Facades\Log;

trait HasMotivationalStats{
    /**
     * Increment new unsigned visit when visit exam created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementMotivational(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementMotivationalData($periodType, $tenantId, $workspaceId);
        }
        return $results;
    }

    /**
     * Increment a specific pending_item counter.
     *
     * @param string $pendingItemKey The pending_items key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementMotivationalData(string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            Log::channel('elasticsearch')->info('incrementMotivationalData called', [
                'period_type' => $periodType,
                'tenant_id' => $tenantId,
                'workspace_id' => $workspaceId,
            ]);

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);
            if (!isset($currentData['motivational_stats'])) {
                Log::channel('elasticsearch')->warning('motivational_stats not found in document', [
                    'period_type' => $periodType,
                    'has_keys' => array_keys($currentData),
                ]);
                return [
                    'success' => false,
                    'error' => "Motivational stats not found"
                ];
            }

            $currentCount = $currentData['motivational_stats']['current'] ?? 0;
            $currentData['motivational_stats']['current'] = $count = $currentCount + $increment;

            Log::channel('elasticsearch')->info('incrementMotivationalData incremented', [
                'period_type' => $periodType,
                'previous_count' => $currentCount,
                'new_count' => $count,
            ]);

            $previousData = $this->getPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);
            $previousCount = (!isset($previousData)) ? 0 : $previousData['motivational_stats']['current'] ?? 0;
            
            $currentData['motivational_stats']['target'] = $previousCount;
            $remaining = $previousCount - $count;
            $percentage = $previousCount == 0 ? 100 : ($count/$previousCount)*100;
            $growth = abs($remaining);
            if ($remaining < 0){
                $growth_percentage = abs($percentage);
            }else{
                $growth_percentage = 0;
            }
            if ($remaining < 0) $remaining = 0;

            $currentData['motivational_stats']['percentage'] = $percentage;
            $currentData['motivational_stats']['remaining'] = $remaining;
            $currentData['motivational_stats']['growth'] = $growth;
            $currentData['motivational_stats']['growth_percentage'] = $growth_percentage;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();
            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            Log::channel('elasticsearch')->info('incrementMotivationalData stored', [
                'period_type' => $periodType,
                'result' => $result,
                'motivational_stats' => $currentData['motivational_stats'],
            ]);

            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment motivational_stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'period_type' => $periodType,
                'tenant_id' => $tenantId ?? null,
                'workspace_id' => $workspaceId ?? null,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}