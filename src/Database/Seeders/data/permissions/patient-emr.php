<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'       => 'Data Pasien dan EMR', 
    'alias'      => 'api.patient-emr',
    'icon'       => 'fluent:patient-20-regular',
    'type'       => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name' => 'api',
    'ordering'   => 1,
    'childs'     => [
        include(__DIR__.'/patient-emr/patient.php'),
        // include(__DIR__.'/patient-emr/visit-patient.php'),
        include(__DIR__.'/patient-emr/visit-registration.php')
    ]
];

