<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class DosageFormSeeder extends Seeder{
    use HasRequestData;

    protected $datas = [
            ['name' => 'Aerosol'],
            ['name' => 'Ekstrak Bubuk'],
            ['name' => 'Ekstrak Cair'],
            ['name' => 'Emulsi'],
            ['name' => 'Gel'],
            ['name' => 'Imunoserum'],
            ['name' => 'Implan'],
            ['name' => 'Inhalasi'],
            ['name' => 'Irigasi'],
            ['name' => 'Kaplet'],
            ['name' => 'Kapsul'],
            ['name' => 'Krim'],
            ['name' => 'Larutan/Sirup'],
            ['name' => 'Pasta'],
            ['name' => 'Plester'],
            ['name' => 'Pulveres'],
            ['name' => 'Pulvis'],
            ['name' => 'Salep'],
            ['name' => 'Tetes Mata'],
            ['name' => 'Suppositoria'],
            ['name' => 'Suspensi'],
            ['name' => 'Tablet'],
            ['name' => 'Vaksin'],
            ['name' => 'Kapsul Lunak'],
            ['name' => 'Kaplet Salut Film'],
            ['name' => 'Tablet Effervescent'],
            ['name' => 'Tablet Pelepasan Terkontrol'],
            ['name' => 'Tablet Salut Enterik'],
            ['name' => 'Pil'],
            ['name' => 'Tablet Salut Selaput'],
            ['name' => 'Granul'],
            ['name' => 'Salep Mata'],
            ['name' => 'Elixir'],
            ['name' => 'Drops'],
            ['name' => 'Suspensi Kering'],
            ['name' => 'Lotion/Solution'],
            ['name' => 'Injeksi'],
            ['name' => 'Injeksi Suspensi Kering'],
            ['name' => 'Tetes Hidung'],
            ['name' => 'Tetes Telinga'],
            ['name' => 'Infus'],
            ['name' => 'Nasal Spray'],
            ['name' => 'Rectal Tube'],
            ['name' => 'Tablet Kunyah'],
            ['name' => 'Tablet Dispersi']
    ];

    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        foreach ($this->datas as $data) {
            app(config('app.contracts.DosageForm'))->prepareStoreDosageForm(
                $this->requestDTO(config('app.contracts.DosageFormData'), $data)
            );
        }
    }
}
