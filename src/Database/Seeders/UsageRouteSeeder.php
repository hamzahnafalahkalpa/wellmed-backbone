<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class UsageRouteSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        ['name' => 'Rektal'],
        ['name' => 'Sublingual'],
        ['name' => 'Parenteral'],
        ['name' => 'Langung ke Organ'],
        ['name' => 'Selaput Perut'],
        ['name' => 'Topikal'],
        ['name' => 'Vaginal'],
        ['name' => 'Nasal'],
        ['name' => 'Okular'],
        ['name' => 'Inhalasi'],
        ['name' => 'Oral'],
        ['name' => 'IM/IV'],
        ['name' => 'IM/SC'],
        ['name' => 'Topikal Kulit'],
        ['name' => 'IM'],
        ['name' => 'IV'],
        ['name' => 'Topikal Mata'],
        ['name' => 'Topikal Telinga'],
        ['name' => 'Cair Luar'],
        ['name' => 'IM/IV/SC'],
        ['name' => 'Topikal Mulut'],
        ['name' => 'Oral/Inhalasi'],
        ['name' => 'Oral/Sublingual/Vaginal']
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.UsageRoute'))->prepareStoreUsageRoute(
                $this->requestDTO(config('app.contracts.UsageRouteData'), $data)
            );
        }
    }
}
