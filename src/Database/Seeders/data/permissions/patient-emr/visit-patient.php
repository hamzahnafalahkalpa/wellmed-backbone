<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Kunjungan Pasien', 
    'alias'           => 'visit-patient',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MENU->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Kelola Kunjungan Pasien', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Kunjungan Pasien', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Kunjungan Pasien', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true,
            'childs'       => [
                include(__DIR__.'/visit-patient/visit-registration.php'),
            ]
        ],
        [
            'name'       => 'Hapus Kunjungan Pasien', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
