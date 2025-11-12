<?php

namespace Projects\WellmedBackbone\Models\ModuleWarehouse\ClassRoom;

use Hanafalah\ModuleClassRoom\Models\ClassRoom\ClassRoom as ClassRoomClassRoom;

class ClassRoom extends ClassRoomClassRoom
{
    protected $casts = [
        'name' => 'string',
        'service_name' => 'string',
        'service_type_id' => 'string',
        'service_type_name' => 'string',
        'service_type_label' => 'string',
        'medic_service_id' => 'string'
    ];

    public function getPropsQuery(): array{
        return [
            'service_type_name'  => 'props->prop_service_type->name',
            'service_type_label' => 'props->prop_service_type->label',
            'medic_service_id'   => 'props->prop_service_type->reference_id'
        ];
    }
}
