<?php

namespace Projects\WellmedBackbone\Schemas\Concerns;

use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Services\SatuSehatDashboardService;

/**
 * Trait HasSatuSehatDashboard
 *
 * Provides helper methods for tracking Satu Sehat dashboard metrics in schemas.
 * Increments wellMedCount when records are created in WellMed.
 */
trait HasSatuSehatDashboard
{
    /**
     * Increment the wellMedCount for a specific resource type in Satu Sehat dashboard.
     * Called when a new record is created in WellMed.
     *
     * @param string $resourceType The resource type (patients, encounter, observation, practitioners, location, organization)
     * @param int $increment Amount to increment (default 1)
     * @return void
     */
    protected function incrementSatuSehatWellMedCount(string $resourceType, int $increment = 1): void
    {
        try {
            if (!config('elasticsearch.enabled', false)) {
                return;
            }

            $dashboardService = app(SatuSehatDashboardService::class);
            $dashboardService->incrementCurrentCount($resourceType, $increment);

        } catch (\Throwable $e) {
            Log::channel('elasticsearch')->error('Failed to increment wellMedCount', [
                'error' => $e->getMessage(),
                'resource_type' => $resourceType
            ]);
        }
    }
}
