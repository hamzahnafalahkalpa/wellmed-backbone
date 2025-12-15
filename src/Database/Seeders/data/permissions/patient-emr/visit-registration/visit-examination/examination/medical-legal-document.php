<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Dokumen Legal Medis', 
    'alias'           => 'medical-legal-document',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MODULE->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Kelola Dokumen Legal Medis', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'         => 'Detail Dokumen Legal Medis', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true
        ],
        [
            'name'       => 'Hapus Dokumen Legal Medis', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
