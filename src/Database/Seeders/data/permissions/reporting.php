<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Pelaporan', 
    'alias'       => 'api.reporting',
    'icon'        => 'oui:reporter',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'ordering'    => 10,
    'childs'      => [
        [
            'name'        => 'Rekap Data Pasien', 
            'alias'       => 'patient-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Kunjungan Pasien', 
            'alias'       => 'visit-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Transaksi Pasien', 
            'alias'       => 'patient-transaction-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Rekap Pembayaran', 
            'alias'       => 'billing-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Aktivitas Medis', 
            'alias'       => 'medical-activity-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Refund dan Diskon', 
            'alias'       => 'refund-and-discount-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Laporan Diagnosa', 
            'alias'       => 'diagnose-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Kunjungan Belum Ditandatangani', 
            'alias'       => 'unsigned-visit-report',
            'icon'        => 'mdi:report-line-shimmer',
            'type'        => Type::PERMISSION->value,
            'show_in_acl' => true,
            'guard_name'  => 'api'
        ]
    ]
];

