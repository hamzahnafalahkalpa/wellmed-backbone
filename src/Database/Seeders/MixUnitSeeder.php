<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class MixUnitSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Puyer'],
        ['name' => 'Sirup'],
        ['name' => 'Salep'],
        ['name' => 'Emulsi'],
        ['name' => 'Suspensi'],
        ['name' => 'Bedak'],
        ['name' => 'Kapsul'],
        ['name' => 'Gel'],
        ['name' => 'Larutan'],
        ['name' => 'Krim'],
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.MixUnit'))->prepareStoreMixUnit(
                $this->requestDTO(config('app.contracts.MixUnitData'), $data)
            );
        }
    }
}
