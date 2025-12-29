<?php

namespace Projects\WellmedBackbone\Schemas\ModuleWarehouse;

use Hanafalah\ModuleWarehouse\Contracts\Data\ModelHasRoomData;
use Hanafalah\ModuleWarehouse\Schemas\Room as SchemasRoom;
use Hanafalah\ModuleWarehouse\Contracts\Data\RoomData;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleWarehouse\Room as ModuleWarehouseRoom;
use Illuminate\Support\Facades\Log;

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
        return $this->room_model = $room_model;
    }

    public function prepareStoreRoom(RoomData $room_dto): Model{
        $this->room_model = $room_model = parent::prepareStoreRoom($room_dto);
        $workspace = tenancy()->tenant->reference;
        if (isset($room_model->ihs_number)){
            try {
                $room_model->ihs_name = $room_model->name." - ".$room_model->getKey();
                $form_payload = [
                    "name" => $room_model->ihs_name,
                    "description" => "-",
                    "location_code" => "RU-".$room_model->getKey(),
                    "organization_code" => config('satu-sehat.organization_id'),
                    "address" => [
                        "use" => "work",
                        "name" => $workspace->setting['address']['name'] ?? 'Unknown Address',
                        // "city" => "Bandung",
                        // "postal_code" => "11290",
                        // "province_code" => "32",
                        // "city_code" => "3212",
                        // "district_code" => "321212",
                        // "village_code" => "3212122013",
                        // "rw" => "4",
                        // "rt" => "50"
                    ],
                    "telecom" => [
                        "work" => [
                            "phone" => ["0812######"],
                            // "email" => ["a@mail.com"]
                        ]
                    ],
                    "physical_type" => [
                        "site" => "Ruang ".$room_model->prop_medic_service['label'] ?? 'Umum',
                    ],
                    "longitude" => 0,
                    "latitude" => 0,
                    "altitude" => 0,
                    "managing_organization_code" => $room_dto->props['ihs_number'] ?? $workspace->integration['satu_sehat']['general']['ihs_number'] ?? null
                ];
                $location_satu_sehat = app(config('app.contracts.LocationSatuSehat'))->useAccessToSatuSehat()
                    ->prepareStoreLocationSatuSehat(
                    $this->requestDTO(
                        config('app.contracts.LocationSatuSehatData'),[
                            'model' => $room_model,
                            'form'  => $form_payload
                        ]
                    )
                );
                $room_model->ihs_number = $location_satu_sehat->response['id'] ?? null;
            } catch (\Throwable $th) {
                Log::channel('satu-sehat')->error($th->getMessage());
            }
        }else{
            $room_model->ihs_number = null;
            $room_model->ihs_name = null;
        }
        $room_model->save();
        return $this->room_model;
    }
    
}