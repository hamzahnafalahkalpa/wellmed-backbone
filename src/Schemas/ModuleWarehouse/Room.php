<?php

namespace Projects\WellmedBackbone\Schemas\ModuleWarehouse;

use Hanafalah\ModuleWarehouse\Contracts\Data\ModelHasRoomData;
use Hanafalah\ModuleWarehouse\Schemas\Room as SchemasRoom;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleWarehouse\Room as ModuleWarehouseRoom;

class Room extends SchemasRoom implements ModuleWarehouseRoom{

    public function createRoom(mixed $room_dto): Model{
        $room_model = $this->usingEntity()->updateOrCreate([
            'id'          => $room_dto->id ?? null,
        ], [
            'building_id'        => $room_dto->building_id,
            'name'               => $room_dto->name,
            'room_number'        => $room_dto->room_number,
            'medic_service_id'   => $room_dto->medic_service_id,
            'service_cluster_id' => $room_dto->service_cluster_id
        ]);
        if (isset($room_dto->employee_ids) && count($room_dto->employee_ids) > 0){
            foreach ($room_dto->employee_ids as $employee_id) {
                $this->schemaContract('model_has_room')->prepareStoreModelHasRoom($this->requestDTO(
                    ModelHasRoomData::class,[
                        'warehouse_type' => 'Room',
                        'warehouse_id' => $room_model->id,
                        'model_type' => 'Employee',
                        'model_id' => $employee_id
                    ]
                ));
            }
        }
        $this->ModelHasRoomModel()->whereNotIn('model_id', $room_dto->employee_ids)
            ->where('model_type','Employee')
            ->where('warehouse_id',$room_model->id)
            ->where('warehouse_type','Room')
            ->delete();
        return $room_model;
    }
}