<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Daftar Invoice',
    'alias'       => 'invoice',
    'icon'        => 'mdi:network-point-of-sale',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Detail Data Invoice',
            'alias'       => 'show',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_data' => true,
            'show_in_acl' => true,
            'childs'      => [
                include_once __DIR__.'/invoice/refund.php',
            ]
        ]
    ]
];

