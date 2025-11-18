<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Supply Chain Management',
    'alias'       => 'supply-chain',
    'icon'        => 'teenyicons:docker-outline',
    'type'        => Type::MENU->value,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/supply-chain/purchase-request.php',
        include __DIR__.'/supply-chain/purchase-order.php',
        include __DIR__.'/supply-chain/purchasing.php',
        include __DIR__.'/supply-chain/receive-order.php',
    ]
];

