<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Daftar Integrasi Satu Sehat',
    'alias'       => 'log-integration',
    'icon'        => 'icon-park-solid:manual-gear',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs' => [
        [
            'name' => 'Ubah Log Integrasi Satu Sehat',
            'alias' => 'update',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name' => 'Hapus Log Integrasi Satu Sehat',
            'alias' => 'destroy',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
