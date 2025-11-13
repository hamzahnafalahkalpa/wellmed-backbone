<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Kunjungan Pasien', 
    'alias'           => 'visit-registration',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MENU->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Kelola Kunjungan Pasien Poli', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Kunjungan Pasien Poli', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Kunjungan Pasien Poli', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true,
            'childs'       => [
                include(__DIR__.'/visit-registration/visit-examination.php'),
                include(__DIR__.'/visit-registration/referral.php'),
            ],
        ],
        [
            'name'       => 'Hapus Poliklinik', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
