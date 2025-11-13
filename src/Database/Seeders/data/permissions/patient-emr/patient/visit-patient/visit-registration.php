<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Data Kunjungan Poliklinik', 
    'alias'           => 'visit-registration',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MODULE->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Kelola Data Kunjungan Poliklinik', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Data Kunjungan Poliklinik', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Data Kunjungan Poliklinik', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true,
            'childs'       => [
                include __DIR__.'/visit-registration/visit-examination.php'
            ]
        ],
        [
            'name'       => 'Hapus Data Kunjungan Poliklinik', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
