<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Komponen Isian Triage', 
    'alias'       => 'gcs-stuff',
    'icon'        => 'fluent:heart-pulse-warning-20-filled',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'     => [
        [
            'name'       => 'Ubah Komponen Triage',
            'alias'      => 'update',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

