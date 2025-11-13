<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class InfrastructureSeeder extends Seeder
{
    use HasRequestData;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = app(config('database.models.Employee'))->where('name','Hamzah')->first();

        $building = $this->schema('Building',[
            'name' => 'Gedung Baru'
        ]);

        if (isset($admin)){
            $rooms = [
                'Umum', 'Instalasi Farmasi', 'Radiologi', 'Instalasi Gawat Darurat',
                'Laboratorium Klinik'
            ];
            foreach ($rooms as $room_name) {
                $this->createRoom([
                    'name' => $room_name,
                    'medic_service_name' => $room_name,
                    'building_id' => $building->getKey(),
                    'phone' => '2938r7684',
                    'employee_ids' => [
                        $admin->getKey()
                    ]
                ]);
            }

            $rooms = [
                ['name' => 'VIP No. 123','medic_service_name' => 'Rawat Inap','class_name' => 'VIP'],
                ['name' => 'VIP No. 124','medic_service_name' => 'Rawat Inap','class_name' => 'VIP'],
                ['name' => 'VIP No. 125','medic_service_name' => 'Rawat Inap','class_name' => 'VIP'],
                ['name' => 'VIP No. 101','medic_service_name' => 'Persalinan','class_name' => 'VIP'],
                ['name' => 'VIP No. 102','medic_service_name' => 'Persalinan','class_name' => 'VIP'],
                ['name' => 'VIP No. 103','medic_service_name' => 'Persalinan','class_name' => 'VIP'],
                ['name' => 'VVIP No. 101','medic_service_name' => 'Persalinan','class_name' => 'VVIP'],
                ['name' => 'VVIP No. 102','medic_service_name' => 'Persalinan','class_name' => 'VVIP'],
                ['name' => 'VVIP No. 103','medic_service_name' => 'Persalinan','class_name' => 'VVIP'],
            ];
            foreach ($rooms as $room) {
                $this->createRoom([
                    'name' => $room['name'],
                    'medic_service_name' => $room['name'],
                    'building_id' => $building->getKey(),
                    'phone' => '2938r7684',
                    'class_room_name' => $room['class_name'],
                    'warehouse_items' => [

                    ]
                ]);
            }
        }
    }

    private function createRoom($attributes){
        $medic_service = $this->model('MedicService')->where('name',$attributes['medic_service_name'])->first();

        if (isset($medic_service)){
            $this->schema('Room',[
                'name' => $attributes['name'],
                'floor' => $attributes['floor'] ?? 1,
                'building_id' => $attributes['building_id'],
                'phone' => $attributes['phone'] ?? null,
                'medic_service_id' => $medic_service->getKey() ?? null,
                'employee_ids' => $attributes['employee_ids'] ?? []
            ]);
        }
    }

    private function model($entity){
        return app(config('database.models.'.$entity));
    }

    private function schema($entity,$attributes): Model{
        return app(config('app.contracts.'.$entity))->{'prepareStore'.$entity}($this->requestDto(config('app.contracts.'.$entity.'Data'),$attributes));
    }
}