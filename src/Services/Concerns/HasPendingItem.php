<?php

namespace Projects\WellmedBackbone\Services\Concerns;
use Illuminate\Support\Str;

trait HasPendingItem{
    /**
     * Increment new unsigned visit when visit exam created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewUnsycedVisit(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementPendingItems('unsynced-patients', $periodType, $tenantId, $workspaceId);
        }
        return $results;
    }

    /**
     * Increment new unsigned visit when visit exam created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewUnsignedVisit(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementPendingItems('unsigned_visits', $periodType, $tenantId, $workspaceId);
        }
        return $results;
    }

    /**
     * Dicrement new unsigned visit when visit exam created.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function decrementNewUnsignedVisit(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->decrementPendingItems('unsigned_visits', $periodType, $tenantId, $workspaceId);
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
    public function incrementPendingItems(string $pendingItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $pendingItemId = $this->mapPendingItemKeyToId($pendingItemKey);
            // Find the statistic in the array
            $pendingItemIdx = $this->findStatisticIndex($currentData['pending_items'], $pendingItemId);
            if ($pendingItemIdx === null) {
                return [
                    'success' => false,
                    'error' => "Pending item '{$pendingItemId}' not found"
                ];
            }

            $currentCount = $currentData['pending_items'][$pendingItemIdx]['count'] ?? 0;
            $currentData['pending_items'][$pendingItemIdx]['count'] = $count = $currentCount + $increment;;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment pending_item', [
                'error' => $e->getMessage(),
                'pending_item' => $pendingItemId,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Decrement a specific pending_item counter.
     *
     * @param string $pendingItemKey The pending_items key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function decrementPendingItems(string $pendingItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);
            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $pendingItemId = $this->mapPendingItemKeyToId($pendingItemKey);

            // Find the statistic in the array
            $pendingItemIdx = $this->findStatisticIndex($currentData['pending_items'], $pendingItemId);
            if ($pendingItemIdx === null) {
                return [
                    'success' => false,
                    'error' => "Statistic '{$pendingItemId}' not found"
                ];
            }

            $currentCount = $currentData['pending_items'][$pendingItemIdx]['count'] ?? 0;
            $currentData['pending_items'][$pendingItemIdx]['count'] = $currentCount - $increment;;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();
            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);
            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to decrement pending_item', [
                'error' => $e->getMessage(),
                'pending_item' => $pendingItemKey,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Find pending_item index by ID in pending_items array.
     *
     * @param array $pending_items
     * @param string $pending_itemId
     * @return int|null
     */
    protected function findPendingItemIndex(array $pending_items, string $pending_itemId): ?int
    {
        foreach ($pending_items as $index => $stat) {
            if (($stat['id'] ?? '') === $pending_itemId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Map pending_item key to ID (for backward compatibility).
     *
     * @param string $key
     * @return string
     */
    protected function mapPendingItemKeyToId(string $key): string
    {
        return match ($key) {
            'unsigned_visits' => 'unsigned-visits',
            'unsynced_patients' => 'unsynced-patients',
            'incomplete_diagnosis' => 'incomplete-diagnosis',
            default => $key
        };
    }

    /**
     * Get default pendint items array with frontend presentation structure.
     *
     * @param string $periodType
     * @return array
     */
    protected function getDefaultPendingItems(string $periodType,? array $data = []): array
    {
        $changeLabel = $this->getChangeLabel($periodType);
        $response = [
             [
                'id' => 'unsigned-visits',
                'label' => 'Unsigned visits',
                'change_label' => $changeLabel, 
                'count' => 0,
                'icon' => 'mdi:file-document-edit-outline', 
                'color' => 'text-orange-600', 
                'link' => '/patient-emr/visit-registration?is_unsigned_visits=1'
             ],
             [
                'id' => 'unsynced-patients',
                'label' => 'Belum tersinkronisasi satu sehat',
                'change_label' => $changeLabel, 
                'count' => 0,
                'icon' => 'mdi:sync-alert', 
                'color' => 'text-red-600', 
                'link' => '/satu-sehat/dashboard'
             ],
             [
                'id' => 'incomplete-diagnosis',
                'label' => 'Tanpa ICD',
                'change_label' => $changeLabel, 
                'count' => 0,
                'icon' => 'mdi:alert-circle', 
                'color' => 'text-amber-600', 
                'link' => '/patient-emr/incomplete-diagnosis'
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