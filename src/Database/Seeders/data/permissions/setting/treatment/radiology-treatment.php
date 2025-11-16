<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Master Tindakan Radiologi',
    'alias'       => 'radiology-treatment',
    'icon'        => 'lsicon:distribution-filled',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs' => [
        [
            'name'       => 'Tambah Tindakan Radiologi',
            'alias'      => 'store',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Detial Tindakan Radiologi',
            'alias'      => 'show',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true,
            'show_in_data' => true
        ],
        [
            'name'       => 'Ubah Tindakan Radiologi',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Hapus Tindakan Radiologi',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
