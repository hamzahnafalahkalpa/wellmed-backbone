<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class PackageFormSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Obat Bebas'],
        ['name' => 'Obat Bebas Terbatas'],
        ['name' => 'Obat Bebas Terbatas P.No.1'],
        ['name' => 'Obat Bebas Terbatas P.No.2'],
        ['name' => 'Obat Bebas Terbatas P.No.3'],
        ['name' => 'Obat Bebas Terbatas P.No.4'],
        ['name' => 'Obat Bebas Terbatas P.No.5'],
        ['name' => 'Obat Bebas Terbatas P.No.6'],
        ['name' => 'Obat Keras'],
        ['name' => 'Obat Luar'],
        ['name' => 'Jamu'],
        ['name' => 'Narkotika'],
        ['name' => 'Obat Herbal'],
        ['name' => 'Obat Herbal Standar'],
        ['name' => 'Psikotropika'],
        ['name' => 'Vito Farmaka']
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.PackageForm'))->prepareStorePackageForm(
                $this->requestDTO(config('app.contracts.PackageFormData'), $data)
            );
        }
    }
}
