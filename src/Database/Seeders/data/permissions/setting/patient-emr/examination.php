<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Patient & Medical Record', 
    'alias'       => 'examination',
    'icon'        => 'healthicons:medical-records-outline',
    'type'        => Type::MODULE->value,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/examination/allergy-stuff.php',
        include __DIR__.'/examination/gcs-stuff.php',
        include __DIR__.'/examination/monitoring-category.php',
        include __DIR__.'/examination/physical-examination-stuff.php',
        include __DIR__.'/examination/triage-stuff.php',
        include __DIR__.'/examination/vital-sign-stuff.php'
    ]
];

