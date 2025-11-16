<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Tindakan Patologi Klinik',
    'alias'       => 'clinical-pathology',
    'icon'        => 'healthicons:clinical-a-outline',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs' => [
        [
            'name'       => 'Tambah Tindakan Patologi Klinik',
            'alias'      => 'store',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Detial Tindakan Patologi Klinik',
            'alias'      => 'show',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true,
            'show_in_data' => true
        ],
        [
            'name'       => 'Ubah Tindakan Patologi Klinik',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Hapus Tindakan Patologi Klinik',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
