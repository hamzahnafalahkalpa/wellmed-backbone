<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Daftar Refund',
    'alias'       => 'refund',
    'icon'        => 'mdi:network-point-of-sale',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Detail Refund',
            'alias'       => 'show',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_data' => true,
            'show_in_acl' => true
        ]
    ]
];

