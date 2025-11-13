<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleMedicService\Contracts\Data\ServiceClusterData;
use Illuminate\Database\Seeder;

class ServiceClusterSeeder extends Seeder
{
    use HasRequestData;

    public function run()
    {
        $arr = [
            [
                'name'  => 'Kluster 1',
                'flag'  => 'ServiceCluster',
                'label' => 'KLUSTER 1',
            ],
            [
                'name' => 'Kluster 2',
                'flag' => 'ServiceCluster',
                'label' => 'KLUSTER 2',
                'childs' => [
                    ['name' => 'Ibu hamil, bersalin, dan nifas', 'flag' => 'ServiceCluster','label' => 'IBU HAMIL BERSALIN NIFAS'],
                    ['name' => 'Balita dan anak pra sekolah', 'flag' => 'ServiceCluster','label' => 'BALITA ANAK PRA SEKOLAH'],
                    ['name' => 'Anak usia sekolah dan remaja (5 - 18 thn)', 'flag' => 'ServiceCluster','label' => 'ANAK USIA SEKOLAH DAN REMAJA'],
                ]
            ],
            [
                'name' => 'Kluster 3',
                'flag' => 'ServiceCluster',
                'label' => 'KLUSTER 3',
                'childs' => [
                    ['name' => 'Pelayanan kesehatan usia dewasa (19 - 59  thn)', 'flag' => 'ServiceCluster','label' => 'KESEHATAN USIA DEWASA'],
                    ['name' => 'Pelayanan kesehatan lansia (> 60 thn)', 'flag' => 'ServiceCluster','label' => 'KESEHATAN LANSIA'],
                ]
            ],
            [
                'name'  => 'Kluster 4',
                'flag'  => 'ServiceCluster',
                'label' => 'KLUSTER 4',
                'childs' => [
                    ['name' => 'Penanggulangan Penyakit Menular', 'flag' => 'ServiceCluster','label' => 'PENANGGULANGAN PENYAKIT MENULAR'],
                ]
            ],
            [
                'name'  => 'Lintas Kluster',
                'flag'  => 'ServiceCluster',
                'label' => 'LINTAS KLUSTER',
            ],
            [
                'name'  => 'Luar Gedung',
                'flag'  => 'ServiceCluster',
                'label' => 'PELAYANAN LUAR GEDUNG',
            ]
        ];
        foreach ($arr as $data) {
            app(config('app.contracts.ServiceCluster'))->prepareStoreServiceCluster($this->requestDTO(ServiceClusterData::class,$data));
        }
    }
}
