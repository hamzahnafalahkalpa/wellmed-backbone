<?php

namespace Projects\WellmedBackbone\Services\Concerns;

trait incrementNewOmzetHasBilling
{
    /**
     * Increment new omzet when billing paid.
     * Updates all period types (daily, weekly, monthly, yearly).
     *
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @return array Results for each period type
     */
    public function incrementNewTotalRevenue(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementBilling('total-revenue', $periodType, $tenantId, $workspaceId);
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
    public function incrementNewTotalBilling(int $amount = 1, ?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementBilling('total-billing', $periodType, $tenantId, $workspaceId, $amount);
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
    public function incrementNewUnpaidBilling(int $amount,?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementBilling('unpaid-billing', $periodType, $tenantId, $workspaceId, $amount);
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
    public function incrementNewPaidBilling(?int $tenantId = null, mixed $workspaceId = null): array
    {
        $results = [];
        $periodTypes = $this->getPeriodTypes();
        foreach ($periodTypes as $periodType) {
            $results[$periodType] = $this->incrementBilling('paid-billing', $periodType, $tenantId, $workspaceId);
        }
        return $results;
    }

    /**
     * Increment a specific billing counter.
     *
     * @param string $billingItemKey The billing key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function incrementBilling(string $billingItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);

            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $billingId = $this->mapBillingKeyToId($billingItemKey);
            // Find the statistic in the array
            $billingIdx = $this->findStatisticIndex($currentData['billing'], $billingId);
            if ($billingIdx === null) {
                return [
                    'success' => false,
                    'error' => "Billing '{$billingId}' not found"
                ];
            }

            $currentCount = $currentData['billing'][$billingIdx]['count'] ?? 0;
            $currentData['billing'][$billingIdx]['count'] = $count = $currentCount + $increment;;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();

            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);

            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment billing', [
                'error' => $e->getMessage(),
                'billing' => $billingId,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Decrement a specific billing counter.
     *
     * @param string $billingItemKey The billing key (e.g., 'unsigned_visits', 'unsynced_patients')
     * @param string $periodType Period type (daily, weekly, monthly, yearly)
     * @param int|null $tenantId
     * @param mixed $workspaceId
     * @param int $increment Amount to increment (default 1)
     * @return array
     */
    public function decrementBilling(string $billingItemKey, string $periodType, ?int $tenantId = null, mixed $workspaceId = null, ?int $increment = 1): array
    {
        try {
            $tenantId = $tenantId ?? tenancy()->tenant->getKey();
            $workspaceId = $workspaceId ?? tenancy()->tenant->reference?->getKey();
            $timestamp = now();

            // Get or create current document
            $currentData = $this->getOrCreateCurrentPeriod($periodType, $tenantId, $workspaceId, $timestamp);
            // Map key to ID (e.g., 'unsigned_visits' -> 'unsigned-visits')
            $billingId = $this->mapBillingKeyToId($billingItemKey);

            // Find the statistic in the array
            $billingIdx = $this->findStatisticIndex($currentData['billing'], $billingId);
            if ($billingIdx === null) {
                return [
                    'success' => false,
                    'error' => "Statistic '{$billingId}' not found"
                ];
            }

            // Increment the statistic
            $currentCount = $currentData['billing'][$billingIdx]['count'] ?? 0;
            $currentData['billing'][$billingIdx]['count'] = $currentCount + $increment;

            // Calculate change from previous period
            $previousData = $this->getPreviousPeriodData($periodType, $tenantId, $workspaceId, $timestamp);
            $previousStatIndex = $this->findStatisticIndex($previousData['billing'], $billingId);
            $previousCount = $previousStatIndex !== null
                ? ($previousData['billing'][$previousStatIndex]['count'] ?? 0)
                : 0;

            $change = $currentData['billing'][$billingIdx]['count'] - $previousCount;
            $currentData['billing'][$billingIdx]['change'] = abs($change);
            $currentData['billing'][$billingIdx]['change_type'] = $change >= 0 ? 'increase' : 'decrease';
            $currentData['billing'][$billingIdx]['percentage_change'] = $previousCount > 0
                ? round((abs($change) / $previousCount) * 100, 1)
                : 100;

            // Update metadata
            $currentData['metadata']['updated_at'] = now()->toIso8601String();
            // Store updated document
            $result = $this->storeCurrentPeriod($currentData, $periodType, $tenantId, $workspaceId, $timestamp);
            return $result;
        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to decrement billing', [
                'error' => $e->getMessage(),
                'billing' => $billingItemKey,
                'period_type' => $periodType
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Find billing index by ID in billing array.
     *
     * @param array $billing
     * @param string $billingId
     * @return int|null
     */
    protected function findBillingIndex(array $billing, string $billingId): ?int
    {
        foreach ($billing as $index => $stat) {
            if (($stat['id'] ?? '') === $billingId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Map billing key to ID (for backward compatibility).
     *
     * @param string $key
     * @return string
     */
    protected function mapBillingKeyToId(string $key): string
    {
        return match ($key) {
            'total_billing' => 'total-billing',
            'paid_billing' => 'paid-billing',
            'unpaid_billing' => 'unpaid-billing',
            'total_revenue' => 'total-revenue',
            default => $key
        };
    }
}