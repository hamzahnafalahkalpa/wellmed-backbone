<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class UsageLocationSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Obat Luar'],
        ['name' => 'Obat Minum']
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.UsageLocation'))->prepareStoreUsageLocation(
                $this->requestDTO(config('app.contracts.UsageLocationData'), $data)
            );
        }
    }
}
