<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Billing',
    'alias'       => 'billing',
    'icon'        => 'streamline-ultimate:self-payment-touch-euro-bold',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        [
            'name'        => 'Tambah Billing Pembayaran',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Billing Pembayaran',
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'        => 'Detail Billing Pembayaran',
            'alias'       => 'show',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_data' => true,
            'show_in_acl' => true,
            'childs'       => [
                include_once __DIR__.'/billing/invoice.php',
            ]
        ],
        [
            'name'       => 'Hapus Billing Pembayaran',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

