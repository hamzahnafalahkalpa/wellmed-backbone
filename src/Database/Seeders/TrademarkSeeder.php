<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class TrademarkSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Generik'],
        ['name' => 'Generik Berlogo'],
        ['name' => 'Generik Bermerk'],
        ['name' => 'Paten'],
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.Trademark'))->prepareStoreTrademark(
                $this->requestDTO(config('app.contracts.TrademarkData'), $data)
            );
        }
    }
}
