<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Pengelolaan Barang', 
    'alias'       => 'item-management',
    'icon'        => 'fluent:tray-item-add-20-filled',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/item-management/composition-unit.php',
        include __DIR__.'/item-management/net-unit.php',
        include __DIR__.'/item-management/selling-form.php',
    ]
];

