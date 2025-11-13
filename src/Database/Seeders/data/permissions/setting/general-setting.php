<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Pengaturan Umum', 
    'alias'       => 'general-setting',
    'icon'        => 'icon-park-solid:manual-gear',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/general-setting/workspace.php',
        include __DIR__.'/general-setting/encoding.php',
    ]
];

