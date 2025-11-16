<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Integrasi Satu Sehat', 
    'alias'       => 'satu-sehat-integration',
    'icon'        => 'icon-park-solid:manual-gear',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api'
];

