<?php

namespace Projects\WellmedBackbone\Config\Dashboard;

/**
 * Clinic-specific dashboard metric definitions.
 *
 * Contains presentation data (labels, icons, colors) for metrics
 * that are stored minimally in Elasticsearch.
 */
class DashboardMetricsConfig
{
    /**
     * Get motivational status metric definitions.
     *
     * @return array<string, array> Map of metric ID to presentation data
     */
    public static function getMotivationalStatus(): array{
        return [
        ];
    }

    /**
     * Get statistics metric definitions.
     *
     * @param string $changeLabel The change label based on period
     * @return array<string, array> Map of metric ID to presentation data
     */
    public static function getStatistics(string $changeLabel): array
    {
        return [
            'patients' => [
                'id' => 'patients',
                'label' => 'Jumlah Pasien',
                'change_label' => $changeLabel,
                'icon' => 'mdi:account-group',
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-cyan-400',
                'bg_light' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'border_color' => 'border-blue-200',
                'is_currency' => false,
            ],
            'new_patients' => [
                'id' => 'new-patients',
                'label' => 'Pasien Baru',
                'change_label' => $changeLabel,
                'icon' => 'mdi:account-plus',
                'color' => 'purple',
                'gradient' => 'from-purple-500 to-pink-400',
                'bg_light' => 'bg-purple-50',
                'text_color' => 'text-purple-600',
                'border_color' => 'border-purple-200',
                'is_currency' => false,
            ],
            'revenue' => [
                'id' => 'revenue',
                'label' => 'Omzet',
                'change_label' => $changeLabel,
                'icon' => 'mdi:cash-multiple',
                'color' => 'emerald',
                'gradient' => 'from-emerald-500 to-teal-400',
                'bg_light' => 'bg-emerald-50',
                'text_color' => 'text-emerald-600',
                'border_color' => 'border-emerald-200',
                'is_currency' => true,
            ],
            'treatment' => [
                'id' => 'treatment',
                'label' => 'Tindakan Dipesankan',
                'change_label' => $changeLabel,
                'icon' => 'mdi:clipboard-list',
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-400',
                'bg_light' => 'bg-orange-50',
                'text_color' => 'text-orange-600',
                'border_color' => 'border-orange-200',
                'is_currency' => false,
            ],
        ];
    }

    /**
     * Get pending items metric definitions.
     *
     * @param string $changeLabel The change label based on period
     * @return array<string, array> Map of metric ID to presentation data
     */
    public static function getPendingItems(string $changeLabel): array
    {
        return [
            'unsigned_visits' => [
                'id' => 'unsigned-visits',
                'label' => 'Unsigned visits',
                'change_label' => $changeLabel,
                'icon' => 'mdi:file-document-edit-outline',
                'color' => 'text-orange-600',
                'link' => '/patient-emr/visit-registration?is_unsigned_visits=1',
            ],
            'unsynced_patients' => [
                'id' => 'unsynced-patients',
                'label' => 'Belum tersinkronisasi satu sehat',
                'change_label' => $changeLabel,
                'icon' => 'mdi:sync-alert',
                'color' => 'text-red-600',
                'link' => '/satu-sehat/dashboard',
            ],
            'incomplete_diagnosis' => [
                'id' => 'incomplete-diagnosis',
                'label' => 'Tanpa ICD',
                'change_label' => $changeLabel,
                'icon' => 'mdi:alert-circle',
                'color' => 'text-amber-600',
                'link' => '/patient-emr/incomplete-diagnosis',
            ],
        ];
    }

    /**
     * Get cashier metric definitions.
     *
     * @param string $changeLabel The change label based on period
     * @return array<string, array> Map of metric ID to presentation data
     */
    public static function getCashier(string $changeLabel): array
    {
        return [
            'revenue' => [
                'id' => 'revenue',
                'label' => 'Omzet',
                'change_label' => $changeLabel,
                'icon' => 'mdi:cash-multiple',
                'color' => 'emerald',
                'gradient' => 'from-emerald-500 to-teal-400',
                'bg_light' => 'bg-emerald-50',
                'text_color' => 'text-emerald-600',
                'border_color' => 'border-emerald-200',
                'is_currency' => true,
            ],
            'unpaid' => [
                'id' => 'unpaid',
                'label' => 'Jumlah Belum Dibayar',
                'change_label' => $changeLabel,
                'icon' => 'mdi:alert-circle',
                'color' => 'red',
                'gradient' => 'from-red-500 to-rose-400',
                'bg_light' => 'bg-red-50',
                'text_color' => 'text-red-600',
                'border_color' => 'border-red-200',
                'is_currency' => true,
            ],
            'total_transactions' => [
                'id' => 'total-transactions',
                'label' => 'Jumlah Transaksi',
                'change_label' => $changeLabel,
                'icon' => 'mdi:receipt-text',
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-cyan-400',
                'bg_light' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'border_color' => 'border-blue-200',
                'is_currency' => false,
            ],
            'pending' => [
                'id' => 'pending',
                'label' => 'Jumlah Pending',
                'change_label' => $changeLabel,
                'icon' => 'mdi:clock-alert',
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-400',
                'bg_light' => 'bg-orange-50',
                'text_color' => 'text-orange-600',
                'border_color' => 'border-orange-200',
                'is_currency' => false,
            ],
        ];
    }

    /**
     * Get billing metric definitions.
     *
     * @param string $changeLabel The change label based on period
     * @return array<string, array> Map of metric ID to presentation data
     */
    public static function getBilling(string $changeLabel): array
    {
        return [
            'total_billing' => [
                'id' => 'total-billing',
                'label' => 'Total Billing',
                'change_label' => $changeLabel,
                'icon' => 'mdi:receipt-text',
                'color' => 'blue',
                'gradient' => 'from-blue-500 to-cyan-400',
                'bg_light' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'border_color' => 'border-blue-200',
                'is_currency' => false,
            ],
            'unpaid_billing' => [
                'id' => 'unpaid-billing',
                'label' => 'Billing Belum Lunas',
                'change_label' => $changeLabel,
                'icon' => 'mdi:clock-alert',
                'color' => 'orange',
                'gradient' => 'from-orange-500 to-amber-400',
                'bg_light' => 'bg-orange-50',
                'text_color' => 'text-orange-600',
                'border_color' => 'border-orange-200',
                'is_currency' => false,
            ],
            'paid_billing' => [
                'id' => 'paid-billing',
                'label' => 'Billing Lunas',
                'change_label' => $changeLabel,
                'icon' => 'mdi:check-circle',
                'color' => 'green',
                'gradient' => 'from-green-500 to-emerald-400',
                'bg_light' => 'bg-green-50',
                'text_color' => 'text-green-600',
                'border_color' => 'border-green-200',
                'is_currency' => false,
            ],
            'total_revenue' => [
                'id' => 'total-revenue',
                'label' => 'Total Pendapatan',
                'change_label' => $changeLabel,
                'icon' => 'mdi:cash-multiple',
                'color' => 'emerald',
                'gradient' => 'from-emerald-500 to-teal-400',
                'bg_light' => 'bg-emerald-50',
                'text_color' => 'text-emerald-600',
                'border_color' => 'border-emerald-200',
                'is_currency' => true,
            ],
        ];
    }

    /**
     * Get workspace integration sync definitions.
     *
     * @param string $changeLabel The change label based on period
     * @return array<string, array> Map of sync flag to presentation data
     */
    public static function getWorkspaceIntegrationSyncs(string $changeLabel): array
    {
        return [
            'encounter' => [
                'flag' => 'encounter',
                'label' => 'Kunjungan',
                'change_label' => $changeLabel,
            ],
            'dispense' => [
                'flag' => 'dispense',
                'label' => 'Resep',
                'change_label' => $changeLabel,
            ],
            'condition' => [
                'flag' => 'condition',
                'label' => 'Diagnosa',
                'change_label' => $changeLabel,
            ],
            'patient' => [
                'flag' => 'patient',
                'label' => 'Pasien',
                'change_label' => $changeLabel,
            ],
            'location' => [
                'flag' => 'location',
                'label' => 'Lokasi/Ruangan',
                'change_label' => $changeLabel,
            ],
            'practitioner' => [
                'flag' => 'practitioner',
                'label' => 'Tenaga Kesehatan',
                'change_label' => $changeLabel,
            ],
        ];
    }

    /**
     * Get the localized change label based on period type.
     *
     * @param string $periodType The period type
     * @return string The localized change label
     */
    public static function getChangeLabel(string $periodType): string
    {
        return match ($periodType) {
            'daily' => 'Dari kemarin',
            'weekly' => 'Dari minggu lalu',
            'monthly' => 'Dari bulan lalu',
            'yearly' => 'Dari tahun lalu',
            default => 'Dari kemarin'
        };
    }
}
