<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleService\Models\Service;

class RadiologySeeder extends \Illuminate\Database\Seeder
{
    use HasRequestData;
    private $__service;
    protected $__datas = [
        ['name' => "Rontgen"],
        ['name' => "CT Scan"],
        ['name' => "MRI"],
        ['name' => "USG"],
        ['name' => "Mammografi"],
        ['name' => "Fluoroskopi"],
        ['name' => "Angiografi"],
        ['name' => "Densitometri Tulang"],
        ['name' => "Radiologi Intervensi"],
        ['name' => "Pemrosesan Gambar (PACS)"]
    ];

    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $this->__service = app(config('database.models.Service', Service::class))->where('reference_type', 'MedicService')
                    ->whereLike('name','Radiologi')->first();
        if (isset($this->__service)){
            foreach ($this->__datas as $data) {
                app(config('app.contracts.Radiology'))->prepareStoreRadiology(
                    $this->requestDTO(config('app.contracts.RadiologyData'), $this->prepare($data)));
            }
        }
    }

    private function prepare($data){
        $arr = [
            "id" => null,
            "name" => $data['name'],
            "medical_service_treatments" => [ //AUTOMATIC LABORATORIUM KLINIK
                [
                    "id"=> null,
                    "service_id" => $this->__service->getKey() //required, GET AUTOLIST - SERVICE LIST (MEDIC SERVICE - LABORATORIUM)
                ]
            ],
            "treatment" => [
                "id" => null,
                "service_label_id" => null, //nullable, GET FORM AUTOLIST - SERVICE LABEL LIST
                "status"=> "ACTIVE", //require, in=> ACTIVE, INACTIVE
            ],
            "childs" => [
                
            ]
        ];
        if (isset($data['childs']) && count($data['childs']) > 0){
            foreach ($data['childs'] as $child) {
                $arr['childs'][] = $this->prepare($child);
            }
        }
        return $arr;
    }
}