<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'       => 'Instalasi Farmasi', 
    'alias'      => 'api.pharmacy-department',
    'icon'       => 'healthicons:pharmacy-24px',
    'type'       => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name' => 'api',
    'ordering'   => 1,
    'childs'     => [
        include(__DIR__.'/pharmacy-department/dispense.php'),
        include(__DIR__.'/pharmacy-department/frontline.php'),
        include(__DIR__.'/pharmacy-department/pharmacy-sale.php'),
    ]
];

