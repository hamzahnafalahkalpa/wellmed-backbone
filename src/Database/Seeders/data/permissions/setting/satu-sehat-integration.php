<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Integrasi Satu Sehat', 
    'alias'       => 'satu-sehat-integration',
    'icon'        => 'icon-park-solid:manual-gear',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Pengaturan Satu Sehat', 
            'alias'       => 'general-integration',
            'icon'        => 'mdi:cloud-access',
            'type'        => Type::MODULE->value,
            'show_in_acl' => true,
            'guard_name'  => 'api',
        ]
    ]
];

