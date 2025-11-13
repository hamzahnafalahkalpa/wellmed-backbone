<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Pengaturan Komponen Tarif', 
    'alias'           => 'tariff-component',
    'icon'            => 'healthicons:coins',
    'type'            => Type::MODULE->value,
    'show_in_acl'     => true,
    'guard_name'      => 'api',
    'childs'          => [
        [
            'name'            => 'Tambah Komponen Tarif',
            'alias'           => 'store',
            'type'            => Type::PERMISSION->value,
            'guard_name'      => 'api',
            'show_in_acl'     => true
        ],
        [
            'name'            => 'Detail Komponen Tarif',
            'alias'           => 'show',
            'type'            => Type::PERMISSION->value,
            'guard_name'      => 'api',
            'show_in_acl'     => true,
            'show_in_data'    => true
        ],
        [
            'name'            => 'Ubah Komponen Tarif',
            'alias'           => 'update',
            'type'            => Type::PERMISSION->value,
            'guard_name'      => 'api'
        ],
        [
            'name'            => 'Hapus Komponen Tarif', 
            'alias'           => 'destroy',
            'type'            => Type::PERMISSION->value,
            'guard_name'      => 'api'
        ]
    ]
];

