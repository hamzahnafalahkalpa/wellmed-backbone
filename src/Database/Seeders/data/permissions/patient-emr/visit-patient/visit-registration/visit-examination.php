<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'           => 'Daftar Kunjungan Tenaga Medis',
    'alias'          => 'visit-examination',
    'icon'           => 'material-symbols:app-registration-outline-sharp',
    'type'           => Type::MENU->value,
    'show_in_acl'    => true,
    'guard_name'     => 'api',
    'childs'         => [
        [
            'name'        => 'Tambah Kunjungan',
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'        => 'Ubah Kunjungan',
            'alias'       => 'update',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true,
            'show_in_data' => true,
            'childs' => [
                include(__DIR__.'/visit-examination/examination.php'),
            ]
        ],
        [
            'name'       => 'Hapus Kunjungan',
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];

