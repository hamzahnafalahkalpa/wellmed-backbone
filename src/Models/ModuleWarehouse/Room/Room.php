<?php

namespace Projects\WellmedBackbone\Models\ModuleWarehouse\Room;

use Hanafalah\ModuleWarehouse\Models\Building\Room as BuildingRoom;
use Projects\WellmedBackbone\Transformers\Room\ShowRoom;
use Projects\WellmedBackbone\Transformers\Room\ViewRoom;

class Room extends BuildingRoom
{
    protected $list = [
        'id',
        'building_id',
        'name',
        'medic_service_id',
        'service_cluster_id',
        "props"
    ];

    protected $casts = [
        'name'                  => 'string',
        'building_name'         => 'string',
        'medic_service_name'    => 'string',
        'service_cluster_name'  => 'string',
    ];

    public function getPropsQuery(): array
    {
        return [
            'building_name' => 'props->prop_building->name',
            'medic_service_name' => 'props->prop_medic_service->name',
            'service_cluster_name' => 'props->prop_service_cluster->name',
        ];
    }

    public function getShowResource(){
        return ShowRoom::class;
    }

    public function getViewResource(){
        return ViewRoom::class;
    }

    public function showUsingRelation(): array{
        return [
            'employees',
            'warehouseItems'
        ];
    }

    public function medicService(){return $this->morphOneModel('ModelHasRoom', 'model');}
    public function serviceCluster(){return $this->morphManyModel('ModelHasRoom', 'model');}
    public function employees(){
        return $this->belongsToManyModel(
            'Employee',
            'ModelHasRoom',
            'warehouse_id',
            'model_id'
        )->where('model_type',$this->EmployeeModel()->getMorphClass())
         ->where('warehouse_type','Room');
    }

    public function rooms(){
        return $this->belongsToManyModel(
            'Room','ModelHasRoom',
            'model_id','warehouse_id'
        )->where('model_type',$this->getMorphClass())
         ->where('warehouse_type','Room');
    }

    public function pharmacy(){
        return $this->hasOneThroughModel(
            'Room', 
            'ModelHasRoom',
            'model_id',
            $this->getKeyName(),
            $this->getKeyName(),
            'warehouse_id'
        )->where('model_type',$this->getMorphClass())
         ->where('warehouse_type','Room');
    }
    //END EIGER SECTION
}
