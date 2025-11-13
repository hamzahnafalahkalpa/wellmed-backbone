<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Aset Finance', 
    'alias'       => 'finance',
    'icon'        => 'mdi:finance',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/finance/tariff-component.php',
        include __DIR__.'/finance/payment-method.php',
    ]
];

