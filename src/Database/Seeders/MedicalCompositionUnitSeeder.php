<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class MedicalCompositionUnitSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => '%'],
        ['name' => 'mcL'],
        ['name' => 'mg/ml'],
        ['name' => 'g'],
        ['name' => 'ml'],
        ['name' => 'mg'],
        ['name' => 'mcg'],
        ['name' => 'IU']
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.MedicalCompositionUnit'))->prepareStoreMedicalCompositionUnit(
                $this->requestDTO(config('app.contracts.MedicalCompositionUnitData'), $data)
            );
        }
    }
}
