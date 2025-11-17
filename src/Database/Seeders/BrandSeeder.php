<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder{
    use HasRequestData;

    protected $datas = [
        [
            'name' => 'Thermo Fisher Scientific',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Eppendorf',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Sigma-Aldrich',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Merck',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Labtech',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Hettich',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Memmert',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Binder',
            'flag' => 'Brand',
        ],
        [
            'name' => 'IKA',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Sartorius',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Sysmex',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Mindray',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Hitachi',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Siemens Healthineers',
            'flag' => 'Brand',
        ],
        [
            'name' => 'GE Healthcare',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Philips Healthcare',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Canon Medical Systems',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Fujifilm',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Shimadzu',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Carestream Health',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Samsung Medison',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Planmeca',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Sirona',
            'flag' => 'Brand',
        ],
        [
            'name' => 'DRGEM',
            'flag' => 'Brand',
        ],
        [
            'name' => 'Agfa',
            'flag' => 'Brand',
        ],
    ];

    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        foreach ($this->datas as $data) {
            app(config('app.contracts.Brand'))->prepareStoreBrand(
                $this->requestDTO(config('app.contracts.BrandData'), $data)
            );
        }
    }
}


