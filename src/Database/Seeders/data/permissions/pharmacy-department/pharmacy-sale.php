<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Apotek', 
    'alias'           => 'pharmacy-sale',
    'icon'            => 'healthicons:inpatient',
    'show_in_acl'     => true,
    'type'            => Type::MENU->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Kelola Antrian Apotek', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Antrian Apotek', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Antrian Apotek', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true,
            'childs'       => [
                include(__DIR__.'/pharmacy-sale/visit-registration.php'),
            ],
        ],
        [
            'name'       => 'Hapus Apotek', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
