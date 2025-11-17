<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Layanan Tindakan', 
    'alias'       => 'treatment',
    'icon'        => 'medical-icon:i-physical-therapy',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/treatment/medical-treatment.php',
        include __DIR__.'/treatment/anatomical-pathology.php',
        include __DIR__.'/treatment/clinical-pathology.php',
        include __DIR__.'/treatment/radiology-treatment.php',
        include __DIR__.'/treatment/sample.php',
    ]
];

