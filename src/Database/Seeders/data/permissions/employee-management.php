<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'       => 'Kepegawaian', 
    'alias'      => 'api.employee-management',
    'icon'       => 'clarity:employee-group-solid',
    'type'       => Type::MENU->value,
    'show_in_acl' => true,
    'guard_name' => 'api',
    'ordering'   => 2,
    'childs'     => [
        include(__DIR__.'/employee-management/employee.php')
    ]
];

