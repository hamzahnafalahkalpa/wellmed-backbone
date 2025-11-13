<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Informasi Faskes',
    'alias'       => 'workspace',
    'icon'        => 'streamline-ultimate:crypto-encryption-key-bold',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs' => [
        [
            'name' => 'Detail Faskes',
            'alias' => 'show',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true
        ],
        [
            'name' => 'Update Informasi Faskes',
            'alias' => 'store',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
