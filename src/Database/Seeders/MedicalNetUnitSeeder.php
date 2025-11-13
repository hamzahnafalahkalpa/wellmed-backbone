<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class MedicalNetUnitSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'l'],
        ['name' => 'ml'],
        ['name' => 'g'],
        ['name' => 'mg'],
        ['name' => 'mcL'],
        ['name' => 'Tablet'],
        ['name' => 'Kaplet'],
        ['name' => 'Kapsul'],
        ['name' => 'Lembar'],
        ['name' => 'Pcs'],
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.MedicalNetUnit'))->prepareStoreMedicalNetUnit(
                $this->requestDTO(config('app.contracts.MedicalNetUnitData'), $data)
            );
        }
    }
}
