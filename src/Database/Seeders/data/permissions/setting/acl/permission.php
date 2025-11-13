<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Hak Akses', 
    'alias'       => 'permission',
    'icon'        => 'icon-park-twotone:key',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api'
];

