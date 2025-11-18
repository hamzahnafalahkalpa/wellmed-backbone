<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'       => 'Stock Opname', 
    'alias'      => 'opname-stock',
    'icon'       => 'lsicon:management-stockout-filled',
    'type'       => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name' => 'api',
    'childs'      => [
        [
            'name'        => 'Tambah Stock Opname',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Ubah Stock Opname',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Detail Stock Opname',
            'alias'      => 'show',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_data' => true,
            'show_in_acl' => true,
            'childs'       => [
                [
                    'name'       => 'Approval Stock Opname',
                    'alias'      => 'approval',
                    'type'       => Type::PERMISSION->value,
                    'guard_name' => 'api'
                ],
                [
                    'name'       => 'Report Stock Opname',
                    'alias'      => 'report',
                    'type'       => Type::PERMISSION->value,
                    'guard_name' => 'api'
                ]
            ]
        ],
        [
            'name'       => 'Hapus Stock Opname',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

