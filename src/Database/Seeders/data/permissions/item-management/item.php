<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Barang Medis',
    'alias'       => 'item',
    'icon'        => 'uil:medical',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Tambah Barang Medis',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Ubah Barang Medis',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Detail Barang Medis',
            'alias'      => 'show',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_data' => true,
            'show_in_acl' => true,
            'childs'      => [
                [
                    'name'        => 'Kartu Stok',
                    'alias'       => 'card-stock',
                    'icon'        => 'lsicon:management-stockout-filled',
                    'type'        => Type::MODULE->value,
                    'show_in_acl' => true,
                    'guard_name'  => 'api',
                ]
            ]
        ],
        [
            'name'       => 'Hapus Barang Medis',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

