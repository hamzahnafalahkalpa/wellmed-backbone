<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Pengelolaan Barang', 
    'alias'       => 'api.item-management',
    'icon'        => 'tabler:building-warehouse',
    'type'        => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'ordering'    => 3,
    'childs'      => [
        include __DIR__.'/item-management/item.php',
        include __DIR__.'/item-management/supply-chain.php',
        include __DIR__.'/item-management/inventory.php',
        include __DIR__.'/item-management/opname-stock.php',
    ]
];

