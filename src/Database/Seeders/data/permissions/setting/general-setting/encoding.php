<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Pengkodean Aplikasi',
    'alias'       => 'encoding',
    'icon'        => 'streamline-ultimate:crypto-encryption-key-bold',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Ubah Pengkodean Aplikasi',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ]
    ]
];
