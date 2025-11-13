<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Patient & Medical Record', 
    'alias'       => 'patient-emr',
    'icon'        => 'healthicons:medical-records-outline',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/patient-emr/examination.php',
    ]
];

