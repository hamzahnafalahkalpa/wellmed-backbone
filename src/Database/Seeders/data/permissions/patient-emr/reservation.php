<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Antrian dan Reservasi', 
    'alias'           => 'reservation',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MENU->value,
    'guard_name'      => 'api',
    'ordering'   => 5,
    'childs'          => [        
        [
            'name'        => 'Kelola Reservasi ', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Reservasi ', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Reservasi ', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true
        ],
        [
            'name'       => 'Hapus Reservasi', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
