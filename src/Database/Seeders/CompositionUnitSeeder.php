<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class CompositionUnitSeeder extends Seeder{
    use HasRequestData;

    private $__item_stuff;

    protected $datas = [
        [
            "name" => "Barang Padat",
            "childs" => [
                [ "name" => "Buah" ],
                [ "name" => "Set" ],
                [ "name" => "Kotak" ],
                [ "name" => "Paket" ],
                [ "name" => "Karton" ],
                [ "name" => "Lusin" ],
                [ "name" => "Ikatan" ],
                [ "name" => "Lembar" ],
                [ "name" => "Strip" ],
                [ "name" => "Tablet" ],
                [ "name" => "Pasang" ],
                [ "name" => "Unit" ]
            ]
        ],
        [
            "name" => "Barang Cair",
            "childs" => [
                [ "name" => "Liter" ],
                [ "name" => "Mililiter" ],
                [ "name" => "Botol" ],
                [ "name" => "Kaleng" ],
                [ "name" => "Drum" ],
                [ "name" => "Toples" ],
                [ "name" => "Barrel" ]
            ]
        ],
        [
            "name" => "Serbuk / Granular",
            "childs" => [
                [ "name" => "Ton" ],
                [ "name" => "Kilogram" ],
                [ "name" => "Gram" ],
                [ "name" => "Miligram" ],
                [ "name" => "Karung" ],
                [ "name" => "Tas" ],
                [ "name" => "Sak" ]
            ]
        ],
        [
            "name" => "Panjang / Gulungan",
            "childs" => [
                [ "name" => "Meter" ],
                [ "name" => "Sentimeter" ],
                [ "name" => "Milimeter" ],
                [ "name" => "Gulungan" ],
                [ "name" => "Reel" ]
            ]
        ],
        [
            "name" => "Curah / Palet",
            "childs" => [
                [ "name" => "Palet" ],
                [ "name" => "Nampan" ],
                [ "name" => "Tray" ],
                [ "name" => "Baki" ]
            ]
        ]
    ];

    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        foreach ($this->datas as $data) {
            app(config('app.contracts.CompositionUnit'))->prepareStoreCompositionUnit(
                $this->requestDTO(config('app.contracts.CompositionUnitData'), $data)
            );
        }
    }
}
