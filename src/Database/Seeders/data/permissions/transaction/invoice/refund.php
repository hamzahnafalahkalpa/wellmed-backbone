<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Daftar Refund',
    'alias'       => 'refund',
    'icon'        => 'mdi:network-point-of-sale',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Tambah Data Refund',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Data Refund',
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Detail Refund',
            'alias'       => 'show',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_data' => true,
            'show_in_acl' => true
        ],
        [
            'name'        => 'Hapus Data Refund',
            'alias'       => 'delete',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ]
    ]
];

