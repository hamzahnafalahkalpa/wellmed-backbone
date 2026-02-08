<?php

namespace Projects\WellmedBackbone\Transformers\Dashboard;

use Hanafalah\LaravelSupport\Resources\Dashboard\BaseDashboardMetricResource;
use Projects\WellmedBackbone\Config\Dashboard\DashboardMetricsConfig;

/**
 * Transformer for statistics ES data to full frontend format.
 *
 * Output is always an indexed array (not associative) for FE consumption.
 */
class StatisticTransformer extends BaseDashboardMetricResource
{
    /**
     * Get statistics definitions for the given period type.
     *
     * @param string $periodType The period type
     * @return array<string, array> Map of metric ID to presentation data
     */
    protected function getDefinitions(string $periodType): array
    {
        $changeLabel = DashboardMetricsConfig::getChangeLabel($periodType);

        return DashboardMetricsConfig::getStatistics($changeLabel);
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
