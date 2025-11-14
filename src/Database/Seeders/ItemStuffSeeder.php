<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class ItemStuffSeeder extends Seeder{
    use HasRequestData;

    protected $datas = [
        'SellingForm' => [
            ['name' => 'Ampul'],
            ['name' => 'Botol'],
            ['name' => 'Dus'],
            ['name' => 'Kaleng'],
            ['name' => 'Kaplet'],
            ['name' => 'Kapsul'],
            ['name' => 'Lembar'],
            ['name' => 'Pack'],
            ['name' => 'Pcs'],
            ['name' => 'Roll'],
            ['name' => 'Blister'],
            ['name' => 'Tablet'],
            ['name' => 'Vial'],
            ['name' => 'Tube'],
            ['name' => 'Sachet'],
            ['name' => 'Unit'],
            ['name' => 'Pouch'],
            ['name' => 'Flask'],
        ],
        'ReceiveUnit' => [
            ['name' => 'Box'],
            ['name' => 'Dus'],
            ['name' => 'Container'],
        ]
    ];

    public function run()
    {
        foreach ($this->datas as $key => $data) {
            foreach ($data as $value) {
                $value['flag'] = $key;
                app(config('app.contracts.ItemStuff'))->prepareStoreItemStuff(
                    $this->requestDTO(config('app.contracts.ItemStuffData'), $value)
                );
            }
        }

    }
}
