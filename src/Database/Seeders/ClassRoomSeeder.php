<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder{
    use HasRequestData;

    protected $datas = [
        [
            'name' => 'Reguler',
            'service' => [
                "id" => null,
                "price" => 100000
            ],
        ],
        [
            'name' => 'VIP',
            'service' => [
                "id" => null,
                "price" => 150000
            ],
        ],
        [
            'name' => 'VVIP',
            'service' => [
                "id" => null,
                "price" => 250000
            ],
        ],
        [
            'name' => 'BPJS I',
            'service' => [
                "id" => null,
                "price" => 250000
            ],
        ],
        [
            'name' => 'BPJS II',
            'service' => [
                "id" => null,
                "price" => 150000
            ],
        ],
        [
            'name' => 'BPJS III',
            'service' => [
                "id" => null,
                "price" => 100000
            ],
        ]
    ];

    public function run()
    {
        foreach (['Rawat Inap', 'Persalinan'] as $service_name){
            $service = app(config('database.models.Service'))->where('name', $service_name)->first();
            if (isset($service)){
                foreach ($this->datas as $data) {
                    $data['service_type_id'] = $service->getKey();
                    app(config('app.contracts.ClassRoom'))->prepareStoreClassRoom(
                        $this->requestDTO(config('app.contracts.ClassRoomData'), $data)
                    );
                }
            }
        }
    }
}
