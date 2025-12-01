<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Integrasi Satu Sehat', 
    'alias'       => 'satu-sehat-integration',
    'icon'        => 'icon-park-solid:manual-gear',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Pengaturan Satu Sehat', 
            'alias'       => 'general-setting',
            'icon'        => 'mdi:cloud-access',
            'type'        => Type::MODULE->value,
            'show_in_acl' => true,
            'guard_name'  => 'api',
            'childs'      => [
                [
                    'name'        => 'Kelola Pengaturan Satu Sehat',
                    'alias'       => 'store',
                    'type'        => Type::PERMISSION->value,
                    'guard_name'  => 'api',
                    'show_in_acl' => true
                ],
                [
                    'name'       => 'Detail Pengaturan',
                    'alias'      => 'show',
                    'type'       => Type::PERMISSION->value,
                    'guard_name' => 'api',
                    'show_in_data' => true,
                    'show_in_acl' => true,
                ],
                [
                    'name'        => 'Integrasi Pasien Satu Sehat',
                    'alias'       => 'patient-integration',
                    'icon'        => 'mdi:cloud-access',
                    'type'        => Type::MODULE->value,
                    'guard_name'  => 'api',
                    'show_in_acl' => true
                ],
                [
                    'name'        => 'Riwayat Kunjungan Satu Sehat',
                    'alias'       => 'log-integration',
                    'icon'        => 'mdi:cloud-access',
                    'type'        => Type::MODULE->value,
                    'guard_name'  => 'api',
                    'show_in_acl' => true
                ]
            ]
        ]
    ]
];

