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
        include __DIR__.'/treatment/medical-treatment.php'
    ]
];

