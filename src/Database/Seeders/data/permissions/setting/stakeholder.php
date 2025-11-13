<?php

use Hanafalah\LaravelPermission\Enums\Permission\Type;

return [
    'name'        => 'Stakeholder', 
    'alias'       => 'stakeholder',
    'icon'        => 'streamline-ultimate:human-resources-workflow-bold',
    'type'        => Type::MODULE->value,
    'show_in_acl' => true,
    'guard_name'  => 'api',
    'childs'      => [
        include __DIR__.'/stakeholder/marital-status.php',
        include __DIR__.'/stakeholder/family-role.php',
        include __DIR__.'/stakeholder/education.php',
    ]
];

