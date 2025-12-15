<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'            => 'Daftar Cetak Surat Pasien', 
    'alias'           => 'letter-queue',
    'icon'            => 'uil:envelopes',
    'type'            => Type::MENU->value,
    'show_in_acl'     => true,
    'guard_name'      => 'api',
    'childs'          => [        
        [
            'name'        => 'Ubah Antrian Cetak Surat Pasien', 
            'alias'       => 'store',
            'type'        => Type::PERMISSION->value,
            'guard_name'  => 'api',
            'show_in_acl' => true
        ],
        [
            'name'         => 'Detail Antrian Cetak Surat Pasien', 
            'alias'        => 'show',
            'type'         => Type::PERMISSION->value,
            'guard_name'   => 'api',
            'show_in_data' => true,
            'show_in_acl'  => true
        ],
        [
            'name'       => 'Hapus Antrian Cetak Surat Pasien', 
            'alias'      => 'destroy',
            'type'       => Type::PERMISSION->value,
            'guard_name' => 'api'
        ]
    ]
];
