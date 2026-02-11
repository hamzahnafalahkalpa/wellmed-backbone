<?php

namespace Projects\WellmedBackbone\Config\SatuSehat;

/**
 * Satu Sehat Dashboard presentation data configuration.
 *
 * Contains presentation data (labels, icons, colors, links) for metrics
 * that are stored minimally in Elasticsearch.
 *
 * ES stores only: id, wellMedCount, satuSehatCount, unsyncedCount, syncedCount, syncPercentage, lastSync
 * This config provides: name, icon, color, description, loggerPath, category
 */
class SatuSehatDashboardConfig
{
    /**
     * Get all resource type definitions with presentation data.
     *
     * @return array<string, array>
     */
    public static function getResourceTypes(): array
    {
        return [
            // Prerequisites
            'organization' => [
                'id' => 'organization',
                'category' => 'Prerequisites',
                'name' => 'Organization',
                'icon' => 'mdi:domain',
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-cyan-400',
                'bg_light' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'border_color' => 'border-blue-200',
                'description' => 'Data organisasi/fasilitas kesehatan',
                'loggerPath' => null,
            ],
            'location' => [
                'id' => 'location',
                'category' => 'Prerequisites',
                'name' => 'Location',
                'icon' => 'mdi:map-marker',
                'color' => 'indigo',
                'gradient' => 'from-indigo-500 to-purple-400',
                'bg_light' => 'bg-indigo-50',
                'text_color' => 'text-indigo-600',
                'border_color' => 'border-indigo-200',
                'description' => 'Data lokasi layanan kesehatan',
                'loggerPath' => null,
            ],
            'practitioners' => [
                'id' => 'practitioners',
                'category' => 'Prerequisites',
                'name' => 'Practitioners',
                'icon' => 'mdi:doctor',
                'color' => 'teal',
                'gradient' => 'from-teal-500 to-emerald-400',
                'bg_light' => 'bg-teal-50',
                'text_color' => 'text-teal-600',
                'border_color' => 'border-teal-200',
                'description' => 'Data tenaga medis dan praktisi',
                'loggerPath' => null,
            ],
            'patients' => [
                'id' => 'patients',
                'category' => 'Prerequisites',
                'name' => 'Patients',
                'icon' => 'mdi:account-group',
                'color' => 'green',
                'gradient' => 'from-green-500 to-emerald-400',
                'bg_light' => 'bg-green-50',
                'text_color' => 'text-green-600',
                'border_color' => 'border-green-200',
                'description' => 'Data pasien yang terdaftar',
                'loggerPath' => '/satu-sehat/data-logger/non-medical',
            ],
            // Interoperability
            'encounter' => [
                'id' => 'encounter',
                'category' => 'Interoperability',
                'name' => 'Encounter',
                'icon' => 'mdi:calendar-check',
                'color' => 'purple',
                'gradient' => 'from-purple-500 to-pink-400',
                'bg_light' => 'bg-purple-50',
                'text_color' => 'text-purple-600',
                'border_color' => 'border-purple-200',
                'description' => 'Data kunjungan pasien',
                'loggerPath' => '/satu-sehat/data-logger/medical',
            ],
            'condition' => [
                'id' => 'condition',
                'category' => 'Interoperability',
                'name' => 'Condition',
                'icon' => 'mdi:medical-bag',
                'color' => 'red',
                'gradient' => 'from-red-500 to-rose-400',
                'bg_light' => 'bg-red-50',
                'text_color' => 'text-red-600',
                'border_color' => 'border-red-200',
                'description' => 'Data diagnosa dan kondisi medis',
                'loggerPath' => '/satu-sehat/data-logger/medical',
            ],
            'observation' => [
                'id' => 'observation',
                'category' => 'Interoperability',
                'name' => 'Observation',
                'icon' => 'mdi:clipboard-pulse',
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-400',
                'bg_light' => 'bg-orange-50',
                'text_color' => 'text-orange-600',
                'border_color' => 'border-orange-200',
                'description' => 'Data hasil pemeriksaan dan observasi',
                'loggerPath' => '/satu-sehat/data-logger/medical',
            ],
        ];
    }

    /**
     * Get presentation data for a specific resource type.
     *
     * @param string $resourceType
     * @return array|null
     */
    public static function getResourceType(string $resourceType): ?array
    {
        return self::getResourceTypes()[$resourceType] ?? null;
    }

    /**
     * Get resource types grouped by category.
     *
     * @return array<string, array>
     */
    public static function getResourceTypesByCategory(): array
    {
        $resourceTypes = self::getResourceTypes();
        $grouped = [];

        foreach ($resourceTypes as $id => $config) {
            $category = $config['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][$id] = $config;
        }

        return $grouped;
    }

    /**
     * Get summary presentation config.
     *
     * @return array
     */
    public static function getSummaryConfig(): array
    {
        return [
            'wellMed' => [
                'label' => 'Total WellMed',
                'description' => 'Total data di WellMed',
                'icon' => 'mdi:database',
                'color' => 'blue',
            ],
            'satuSehat' => [
                'label' => 'Total Satu Sehat',
                'description' => 'Total data tersinkronisasi',
                'icon' => 'mdi:cloud-check',
                'color' => 'green',
            ],
            'unsynced' => [
                'label' => 'Belum Sinkron',
                'description' => 'Total data belum tersinkronisasi',
                'icon' => 'mdi:sync-alert',
                'color' => 'orange',
            ],
            'synced' => [
                'label' => 'Tersinkronisasi',
                'description' => 'Total data tersinkronisasi',
                'icon' => 'mdi:check-circle',
                'color' => 'emerald',
            ],
            'percentage' => [
                'label' => 'Persentase Sinkronisasi',
                'description' => 'Persentase data tersinkronisasi',
                'icon' => 'mdi:percent',
                'color' => 'purple',
            ],
        ];
    }

    /**
     * Get all resource type IDs.
     *
     * @return array<string>
     */
    public static function getResourceTypeIds(): array
    {
        return array_keys(self::getResourceTypes());
    }
}
