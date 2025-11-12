<?php

namespace Projects\WellmedBackbone\Data\ModuleWarehouse;

use Hanafalah\ModuleMedicService\Enums\Label;
use Hanafalah\ModuleWarehouse\Data\RoomData as DataRoomData;
use Projects\WellmedBackbone\Contracts\Data\ModuleWarehouse\RoomData as DataModuleWarehouseRoomData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class RoomData extends DataRoomData implements DataModuleWarehouseRoomData{
    #[MapInputName('as_pharmacy')]
    #[MapName('as_pharmacy')]
    public mixed $as_pharmacy = false;

    #[MapInputName('medic_service_id')]
    #[MapName('medic_service_id')]
    public mixed $medic_service_id = null;

    #[MapInputName('service_cluster_id')]
    #[MapName('service_cluster_id')]
    public mixed $service_cluster_id = null;

    #[MapInputName('employee_ids')]
    #[MapName('employee_ids')]
    public ?array $employee_ids = null;

    #[MapInputName('employees')]
    #[MapName('employees')]
    public ?array $employees = null;

    public static function before(array &$attributes){
        $new = static::new();

        $attributes['employees'] ??= [];
        $model_has_rooms = &$attributes['model_has_rooms'];
        $employees = [];
        if (isset($attributes['employee_ids']) && count($attributes['employee_ids']) > 0){
            $employees = $attributes['employee_ids'];
            foreach ($attributes['employee_ids'] as $employee_id) {
                $model_has_rooms[] = [
                    'model_type' => 'Employee',
                    'model_id' => $employee_id
                ];
            }
        }
        
        if (isset($attributes['employees']) && count($attributes['employees']) > 0){
            foreach ($attributes['employees'] as $employee) {
                $employee[] = $employee['id'];
                $model_has_rooms[] = [
                    'model_type' => 'Employee',
                    'model_id' => $employee['id']
                ];
            }
        }

        $attributes['prop_employees'] = [];
        $employees = $new->EmployeeModel()->whereIn('id', $employees)->get();
        foreach ($employees as $employee) {
            $attributes['prop_employees'][] = [
                'id' => $employee->id,
                'name' => $employee->name
            ];
        }
        parent::before($attributes);
    }

    public static function after(mixed $data): RoomData{
        $new = static::new();

        $props = &$data->props;

        $medic_service = $new->MedicServiceModel();
        $medic_service = isset($data->medic_service_id) ? $medic_service->findOrFail($data->medic_service_id) : $medic_service;
        $props['prop_medic_service'] = $medic_service->toViewApi()->only(['id','name']);

        $data->as_pharmacy = (isset($medic_service) && $medic_service->label == Label::PHARMACY_UNIT->value);

        $service_cluster = $new->ServiceClusterModel();
        $service_cluster = isset($data->service_cluster_id) ? $service_cluster->findOrFail($data->service_cluster_id) : $service_cluster;
        $props['prop_service_cluster'] = $service_cluster->toViewApi()->only(['id','name']);

        $data = parent::after($data);
        return $data;
    }
}