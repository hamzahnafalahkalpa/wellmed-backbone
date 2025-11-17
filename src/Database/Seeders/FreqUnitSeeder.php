<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class FreqUnitSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Injeksi'],
        ['name' => 'Kaplet'],
        ['name' => 'Kapsul'],
        ['name' => 'Oles'],
        ['name' => 'Sachet'],
        ['name' => 'ml'],
        ['name' => 'Suppositoria'],
        ['name' => 'Tablet'],
        ['name' => 'Tetes'],
        ['name' => 'Puyer'],
        ['name' => 'Sendok']
    ];

    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        foreach ($this->datas as $data) {
            app(config('app.contracts.FreqUnit'))->prepareStoreFreqUnit(
                $this->requestDTO(config('app.contracts.FreqUnitData'), $data)
            );
        }
    }
}
