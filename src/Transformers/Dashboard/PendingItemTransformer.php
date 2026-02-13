<?php

namespace Projects\WellmedBackbone\Transformers\Dashboard;

use Hanafalah\LaravelSupport\Resources\Dashboard\BaseDashboardMetricResource;
use Projects\WellmedBackbone\Config\Dashboard\DashboardMetricsConfig;

/**
 * Transformer for pending items ES data to full frontend format.
 *
 * Output is always an indexed array (not associative) for FE consumption.
 */
class PendingItemTransformer extends BaseDashboardMetricResource
{
    /**
     * Get pending items definitions for the given period type.
     *
     * @param ?string $periodType The period type
     * @return array<string, array> Map of metric ID to presentation data
     */
    protected function getDefinitions(?string $periodType = null): array
    {
        $changeLabel = DashboardMetricsConfig::getChangeLabel($periodType);

        return DashboardMetricsConfig::getPendingItems($changeLabel);
    }

    /**
     * Merge ES data with presentation definition.
     *
     * Pending items have a simpler structure without change calculations.
     *
     * @param array $item ES metric data
     * @param array $definition Presentation definition
     * @return array Merged data
     */
    protected function mergeWithPresentation(array $item, array $definition): array
    {
        return array_merge($definition, [
            'id' => $item['id'] ?? $definition['id'],
            'count' => (int) ($item['count'] ?? 0),
            'created_at' => $item['created_at'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
        ]);
    }

    /**
     * Get change label based on period type.
     *
     * @param string $periodType The period type
     * @return string The change label (Indonesian)
     */
    protected function getChangeLabel(string $periodType): string
    {
        return DashboardMetricsConfig::getChangeLabel($periodType);
    }
}
