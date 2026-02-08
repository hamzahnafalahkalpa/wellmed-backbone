<?php

namespace Projects\WellmedBackbone\Transformers\Dashboard;

use Projects\WellmedBackbone\Config\Dashboard\DashboardMetricsConfig;

/**
 * Transformer for workspace integration ES data to full frontend format.
 *
 * Output syncs as indexed array (not associative) for FE consumption.
 */
class WorkspaceIntegrationTransformer
{
    /**
     * Transform workspace integration ES data.
     *
     * @param array $esData ES workspace integration data
     * @param string $periodType The period type
     * @return array Transformed data with presentation fields
     */
    public function transform(array $esData, string $periodType): array
    {
        $changeLabel = DashboardMetricsConfig::getChangeLabel($periodType);
        $syncDefinitions = DashboardMetricsConfig::getWorkspaceIntegrationSyncs($changeLabel);

        // Merge base integration data
        $result = array_merge([
            'flag' => 'satu-sehat',
            'label' => 'Satu Sehat',
        ], $esData);

        // Transform syncs if present - output as indexed array
        if (isset($esData['syncs']) && is_array($esData['syncs'])) {
            $result['syncs'] = $this->transformSyncs($esData['syncs'], $syncDefinitions);
        }

        return $result;
    }

    /**
     * Transform syncs array with presentation data.
     *
     * Output is always an indexed array for FE consumption.
     *
     * @param array $syncs ES syncs data
     * @param array $definitions Sync definitions
     * @return array Transformed syncs as indexed array
     */
    protected function transformSyncs(array $syncs, array $definitions): array
    {
        $result = [];

        foreach ($syncs as $sync) {
            $flag = $sync['flag'] ?? '';

            if (isset($definitions[$flag])) {
                $result[] = array_merge($definitions[$flag], [
                    'flag' => $flag,
                    'progress' => $sync['progress'] ?? 0,
                    'last_updated_at' => $sync['last_updated_at'] ?? now()->format('Y-m-d H:i:s'),
                    'from' => $sync['from'] ?? 0,
                    'to' => $sync['to'] ?? 0,
                    'success_count' => $sync['success_count'] ?? 0,
                    'failed_count' => $sync['failed_count'] ?? 0,
                    'created_at' => $sync['created_at'] ?? null,
                    'updated_at' => $sync['updated_at'] ?? null,
                ]);
            } else {
                // Return sync as-is if no definition found
                $result[] = $sync;
            }
        }

        return $result;
    }

    /**
     * Get default workspace integration structure for frontend.
     *
     * Output syncs as indexed array.
     *
     * @param string $periodType The period type
     * @return array Default workspace integration data
     */
    public function getDefaultForFrontend(string $periodType): array
    {
        $changeLabel = DashboardMetricsConfig::getChangeLabel($periodType);
        $syncDefinitions = DashboardMetricsConfig::getWorkspaceIntegrationSyncs($changeLabel);

        $syncs = [];
        foreach ($syncDefinitions as $flag => $definition) {
            $syncs[] = array_merge($definition, [
                'flag' => $flag,
                'progress' => 0,
                'last_updated_at' => now()->format('Y-m-d H:i:s'),
                'from' => 0,
                'to' => 0,
                'success_count' => 0,
                'failed_count' => 0,
            ]);
        }

        return [
            'flag' => 'satu-sehat',
            'label' => 'Satu Sehat',
            'progress' => 0,
            'last_updated_at' => now()->format('Y-m-d H:i:s'),
            'from' => 0,
            'to' => 0,
            'general' => [
                'ihs_number' => null,
            ],
            'syncs' => $syncs,
            'logs' => [],
        ];
    }
}
