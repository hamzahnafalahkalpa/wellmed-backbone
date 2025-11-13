<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Komponen Isian Tanda Vital', 
    'alias'       => 'gcs-stuff',
    'icon'        => 'mdi:clipboard-vitals',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'     => [
        [
            'name'       => 'Ubah Komponen Tanda Vital',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

