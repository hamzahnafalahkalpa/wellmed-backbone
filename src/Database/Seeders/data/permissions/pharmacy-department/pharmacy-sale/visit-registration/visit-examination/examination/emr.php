<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Rekam Medis', 
    'alias'           => 'medical-record',
    'icon'            => 'healthicons:outpatient-department',
    'show_in_acl'     => true,
    'type'            => Type::MODULE->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'         => 'Detail Rekam Medis', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true
        ]
    ]
];
