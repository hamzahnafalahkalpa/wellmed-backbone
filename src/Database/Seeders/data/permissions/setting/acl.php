<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Hak Akses Pengguna', 
    'alias'       => 'acl',
    'icon'        => 'mdi:cloud-access',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/acl/role.php',
        include __DIR__.'/acl/permission.php'
    ]
];

