<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Transaksi', 
    'alias'       => 'api.transaction',
    'icon'        => 'hugeicons:transaction-history',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'ordering'    => 10,
    'childs'      => [
        include __DIR__.'/transaction/point-of-sale.php',
        include __DIR__.'/transaction/billing.php',
        // include __DIR__.'/transaction/invoice.php',
        include __DIR__.'/transaction/refund.php',
        // include __DIR__.'/transaction/deposit.php',
    ]
];

