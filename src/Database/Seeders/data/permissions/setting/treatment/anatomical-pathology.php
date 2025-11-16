<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Tindakan Patologi Anatomi',
    'alias'       => 'anatomical-pathology',
    'icon'        => 'game-icons:anatomy',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs' => [
        [
            'name'       => 'Tambah Tindakan Patologi Anatomi',
            'alias'      => 'store',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true
        ],
        [
            'name'       => 'Detial Tindakan Patologi Anatomi',
            'alias'      => 'show',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true,
            'show_in_data' => true
        ],
        [
            'name'       => 'Ubah Tindakan Patologi Anatomi',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name'       => 'Hapus Tindakan Patologi Anatomi',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
