<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'           => 'Pemeriksaan',
    'alias'          => 'examination',
    'icon'           => 'medical-icon:internal-medicine',
    'type'           => Type::MENU->value,
    'show_in_acl'    => true,
    'guard_name'     => 'api',
    'childs'         => [
        [
            'name'        => 'Tambah Pemeriksaan',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Pemeriksaan',
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true,
            'show_in_data' => true
        ],
        [
            'name'       => 'Hapus Pemeriksaan',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

