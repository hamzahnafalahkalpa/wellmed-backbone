<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Riwayat Billing',
    'alias'       => 'billing',
    'icon'        => 'streamline-ultimate:cash-payment-bills-bold',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'ordering'    => 2,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Detail Data Billing',
            'alias'       => 'show',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_data' => true,
            'show_in_acl' => true,
            'childs'      => [
                include_once __DIR__.'/billing/invoice.php',
            ]
        ]
    ]
];

