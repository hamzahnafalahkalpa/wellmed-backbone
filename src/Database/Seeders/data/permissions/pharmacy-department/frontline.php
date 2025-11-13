<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Antrian Resep Pasien', 
    'alias'           => 'dispense',
    'icon'            => 'majesticons:script-prescription',
    'show_in_acl'     => true,
    'type'            => Type::MENU->value,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Ubah Antrian Antrian Resep Pasien', 
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api'
        ],
        [
            'name'         => 'Detail Antrian Antrian Resep Pasien', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true,
            'childs'       => [
                include(__DIR__.'/pharmacy-sale/visit-registration.php')
            ],
        ]
    ]
];
