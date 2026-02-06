<?php

namespace Projects\WellmedBackbone\Services\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasCashier
{
    /**
     * Increment new omzet when billing paid.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewOmzet(int $omzet, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementCashier('revenue', $periodType, $tenantId, $workspaceId, $omzet);
        }
        return $results;
    }

    /**
     * Increment new unpaid when exam created and singed.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int $amount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewUnpaid(int $amount, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementCashier('unpaid', $periodType, $tenantId, $workspaceId, $amount);
        }
        return $results;
    }

    /**
     * Increment new transaction when exam created visit patient.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int $amount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewTransaction(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementCashier('total-transactions', $periodType, $tenantId, $workspaceId);
        }
        return $results;
    }

    /**
     * Increment new transaction when exam created visit patient.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int $amount
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewPending(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementCashier('pending', $periodType, $tenantId, $workspaceId);
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
    public function decrementNewUnpaid(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->decrementCashier('unpaid', $periodType, $tenantId, $workspaceId);
        }
        return $results;
    }

    /**
     * Increment a specific cashier counter.
     *
     * @param string $cashierItemKey The cashier key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementCashier(string $cashierItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $cashierId = $this->mapCashierKeyToId($cashierItemKey);

            $cashierIdx = $this->findCashierIndex($currentData['cashier'], $cashierId);
            if ($cashierIdx === null) {
                return [
                    'success' => false,
                    'error' => "Cashier '{$cashierId}' not found"
                ];
            }
            $currentCount = $currentData['cashier'][$cashierIdx]['count'] ?? 0;
            $currentData['cashier'][$cashierIdx]['count'] = $count = $currentCount + $increment;;


            $previousData = $this->getPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);
            $previousStatIndex = $this->findCashierIndex($previousData['cashier'], $cashierId);
            $previousCount = $previousStatIndex !== null
                ? ($previousData['cashier'][$previousStatIndex]['count'] ?? 0)
                : 0;

            $change = $currentData['cashier'][$cashierIdx]['count'] - $previousCount;
            $currentData['cashier'][$cashierIdx]['change'] = abs($change);
            $currentData['cashier'][$cashierIdx]['change_type'] = $change >= 0 ? 'increase' : 'decrease';
            $currentData['cashier'][$cashierIdx]['percentage_change'] = $previousCount > 0
                ? round((abs($change) / $previousCount) * 100, 1)
                : 100;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment cashier', [
                'error' => $e->getMessage(),
                'cashier' => $cashierId,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Increment a specific cashier counter.
     *
     * @param string $cashierItemKey The cashier key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementCashierWithModel(Model $model, string $cashierItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $cashierId = $this->mapCashierKeyToId($cashierItemKey);
            // Find the statistic in the array
            $cashierIdx = $this->findCashierIndex($currentData['cashier'], $cashierId);
            if ($cashierIdx === null) {
                return [
                    'success' => false,
                    'error' => "Cashier '{$cashierId}' not found"
                ];
            }

            $currentCount = $currentData['cashier'][$cashierIdx]['count'] ?? 0;
            $currentData['cashier'][$cashierIdx]['count'] = $count = $currentCount + $increment;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment cashier', [
                'error' => $e->getMessage(),
                'cashier' => $cashierId,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Decrement a specific cashier counter.
     *
     * @param string $cashierItemKey The cashier key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function decrementCashier(string $cashierItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);
            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $cashierId = $this->mapCashierKeyToId($cashierItemKey);

            // Find the statistic in the array
            $cashierIdx = $this->findCashierIndex($currentData['cashier'], $cashierId);
            if ($cashierIdx === null) {
                return [
                    'success' => false,
                    'error' => "Statistic '{$cashierId}' not found"
                ];
            }

            // Increment the statistic
            $currentCount = $currentData['cashier'][$cashierIdx]['count'] ?? 0;
            $currentData['cashier'][$cashierIdx]['count'] = $currentCount + $increment;

            // Calculate change from previous period
            $previousData = $this->getPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);
            $previousStatIndex = $this->findCashierIndex($previousData['cashier'], $cashierId);
            $previousCount = $previousStatIndex !== null
                ? ($previousData['cashier'][$previousStatIndex]['count'] ?? 0)
                : 0;

            $change = $currentData['cashier'][$cashierIdx]['count'] - $previousCount;
            $currentData['cashier'][$cashierIdx]['change'] = abs($change);
            $currentData['cashier'][$cashierIdx]['change_type'] = $change >= 0 ? 'increase' : 'decrease';
            $currentData['cashier'][$cashierIdx]['percentage_change'] = $previousCount > 0
                ? round((abs($change) / $previousCount) * 100, 1)
                : 100;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();
            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);
            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to decrement cashier', [
                'error' => $e->getMessage(),
                'cashier' => $cashierItemKey,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Find cashier index by ID in cashier array.
     *
     * @param array $cashier
     * @param string $cashierId
     * @return int|null
     */
    protected function findCashierIndex(array $cashier, string $cashierId): ?int
    {
        foreach ($cashier as $index => $stat) {
            if (($stat['id'] ?? '') === $cashierId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Map cashier key to ID (for backward compatibility).
     *
     * @param string $key
     * @return string
     */
    protected function mapCashierKeyToId(string $key): string
    {
        return match ($key) {
            'total_transactions' => 'total-transactions',
            default => $key
        };
    }
}