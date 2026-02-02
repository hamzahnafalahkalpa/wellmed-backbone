<?php

namespace Projects\WellmedBackbone\Schemas\ModuleWarehouse;

use Hanafalah\ModuleWarehouse\Contracts\Data\ModelHasRoomData;
use Hanafalah\ModuleWarehouse\Schemas\Room as SchemasRoom;
use Hanafalah\ModuleWarehouse\Contracts\Data\RoomData;
use Illuminate\Database\Eloquent\Model;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleWarehouse\Room as ModuleWarehouseRoom;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Jobs\SendLocationToSatuSehatJob;

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
        $this->prepareStoreSatuSehatLocation($room_dto,$room_model);
        return $this->room_model;
    }

    public function prepareStoreSatuSehatLocation(mixed $dto,Model $room_model){
        $payload = $this->prepareSatuSehatPayload($dto,$room_model);
        $this->dispatchSatuSehatSync($room_model, $payload);
    }

    /**
     * Prepare the payload for Satu Sehat integration.
     */
    private function prepareSatuSehatPayload(mixed $dto,Model &$room_model): array
    {
        $existingPayload = $room_model->locationSatuSehat?->payload ?? [];
        $basePayload = $this->buildBaseLocationPayload($dto,$room_model);
        $payload = array_merge($existingPayload, $basePayload);
        return $payload;
    }
    
    /**
     * Build base location payload with essential location information.
     */
    private function buildBaseLocationPayload(mixed &$dto, Model &$room_model): array
    {
        $workspace = tenancy()->tenant->reference;
        $location_code = $room_model->getKey().' - '.Str::orderedUuid()->toString();
        $room_model->ihs_name = $room_model->name." - ".$location_code;
        return [
            'name'       => $room_model->ihs_name,
            "description" => "-",
            "location_code" => "RU-".$location_code,
            'organization_code' => config('satu-sehat.organization_id'),
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
                    "phone" => [$room_model->phone ?? "0812######"],
                    // "email" => ["a@mail.com"]
                ]
            ],
            "physical_type" => [
                "site" => "Ruang ".$room_model->prop_medic_service['label'] ?? 'Umum',
            ],
            "longitude" => 0,
            "latitude" => 0,
            "altitude" => 0,
            "managing_organization_code" => config('satu-sehat.organization_id') ?? null
            // "managing_organization_code" => $dto->props['ihs_number'] ?? $workspace->integration['satu_sehat']['general']['ihs_number'] ?? config('satu-sehat.organization_id') ?? null
        ];
    }

    /**
     * Dispatch location data to Satu Sehat via async job if enabled.
     */
    private function dispatchSatuSehatSync(Model $room_model, array $payload): void
    {
        if (!config('module-warehouse.satu-sehat.enable', true)) {
            return;
        }

        $tenant_id = tenancy()->tenant->getKey();
        $room_id = $room_model->getKey();
        try {
            dispatch(new SendLocationToSatuSehatJob(
                $tenant_id,
                $room_id,
                $payload
            ))->onQueue('satusehat')->onConnection('sync');
            // ->onQueue('satusehat')->onConnection(config('queue.default','rabbitmq'));

            Log::channel('satu-sehat')->info('Patient queued for Satu Sehat sync', [
                'room_id' => $room_id,
                'tenant_id' => $tenant_id
            ]);
        } catch (\Throwable $exception) {
            dd($exception->getMessage());
            Log::channel('satu-sehat')->error('Failed to queue location for Satu Sehat', [
                'room_id' => $room_id,
                'error' => $exception->getMessage()
            ]);
        }
    }
}