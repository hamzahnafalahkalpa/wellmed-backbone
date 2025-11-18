<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Purchasing',
    'alias'       => 'purchasing',
    'icon'        => 'bxs:purchase-tag',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Tambah Purchasing',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Ubah Purchasing',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Detail Purchasing',
            'alias'      => 'show',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_data' => true,
            'show_in_acl' => true,
            'childs'      => [
                [
                    'name'       => 'Approval Purchasing',
                    'alias'      => 'approval',
                    'type'       => Type::PERMISSION->value,
                    'guard_name' => 'api'
                ],
                [
                    'name'       => 'Report Purchasing',
                    'alias'      => 'report',
                    'type'       => Type::PERMISSION->value,
                    'guard_name' => 'api'
                ]
            ]
        ],
        [
            'name'       => 'Hapus Purchasing',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

