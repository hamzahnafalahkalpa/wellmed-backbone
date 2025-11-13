<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Master Data Pendidikan',
    'alias'       => 'education',
    'icon'        => 'tdesign:education-filled',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs' => [
        [
            'name' => 'Tambah Master Pendidikan',
            'alias' => 'store',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api',
            'show_in_acl' => true
        ],
        [
            'name' => 'Ubah Master Pendidikan',
            'alias' => 'update',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api'
        ],
        [
            'name' => 'Hapus Master Pendidikan',
            'alias' => 'destroy',
            'type' => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
