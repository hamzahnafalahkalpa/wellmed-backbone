<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class PurchaseLabelSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $model = app(config('database.models.PurchaseLabel'));

        $datas = [
            ['name' => 'Belanja Obat','label'=> 'Medicine'],
            ['name' => 'Belanja Alkes','label'=> 'Medic Tool'],
            ['name' => 'Belanja Zat Aktif Lab','label'=> 'Reagent'],
            ['name' => 'Alat tulis kantor (ATK)','label' => 'Office Supply'],
            ['name' => 'Belanja alat lab','label' => 'Healthcare Equipment'],
            ['name' => 'Belanja alat radiologi','label' => 'Healthcare Equipment'],
            ['name' => 'Barang lainnya','label' => 'Stuff Supply'],
        ];

        foreach ($datas as $data) {
            $model::updateOrCreate([
                'name' => $data['name'],
                'flag' => 'PurchaseLabel',
                'label' => $data['label']
            ]);
        }
    }
}
