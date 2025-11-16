<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'          => 'Data Pegawai',
    'alias'         => 'employee',
    'icon'          => 'fluent:people-team-20-regular',
    'type'          => Type::MENU->value,
    'show_in_acl'   => true,
    'guard_name'    => 'api',
    'childs'        => [
        [
            'name'       => 'Tambah Pegawai',
            'alias'      => 'store',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Edit Pegawai',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Hapus Pegawai',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
    ]
];
