<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Hak Akses Pemeriksaan', 
    'alias'           => 'examination',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MODULE->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Kelola Hak Akses Pemeriksaan', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Hak Akses Pemeriksaan', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Hak Akses Pemeriksaan', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true,
            'childs'       => [
                include __DIR__.'/examination/emr.php',
                include __DIR__.'/examination/anamnesa.php',
                include __DIR__.'/examination/treatment.php',
                include __DIR__.'/examination/referral.php',
                include __DIR__.'/examination/prescription.php',
                include __DIR__.'/examination/support.php',
                include __DIR__.'/examination/populated-form.php',
                include __DIR__.'/examination/practitioner.php'
            ]
        ],
        [
            'name'       => 'Hapus Hak Akses Pemeriksaan', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
