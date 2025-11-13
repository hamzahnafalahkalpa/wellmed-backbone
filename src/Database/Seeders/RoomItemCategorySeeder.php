<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class RoomItemCategorySeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Tempat Tidur','label' => 'Bed'],
        ['name' => 'Mesin ECG','label' => 'ECG'],
        ['name' => 'Defibrillator','label' => 'Defibrillator'],
        ['name' => 'Monitor Pasien','label' => 'Patient Monitor'],
        ['name' => 'Oxygen Cylinder','label' => 'Oxygen Cylinder'],
        ['name' => 'Nebulizer','label' => 'Nebulizer'],
        ['name' => 'Ventilator','label' => 'Ventilator'],
        ['name' => 'USG','label' => 'USG'],
        ['name' => 'Pompa Infus','label' => 'Infusion Pump'],
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.RoomItemCategory'))->prepareStoreRoomItemCategory(
                $this->requestDTO(config('app.contracts.RoomItemCategoryData'), $data)
            );
        }
    }
}
