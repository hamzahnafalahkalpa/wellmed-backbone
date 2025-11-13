<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;

use Illuminate\Database\Seeder;

class PatientOccupationSeeder extends Seeder
{
    public $occupations = [
        [
            "name" => "Pekerjaan Formal",
            "childs" => [
                ["name" => "PNS"],
                ["name" => "TNI"],
                ["name" => "Polri"],
                ["name" => "Pegawai Swasta"],
                ["name" => "BUMN"],
                ["name" => "Guru"],
                ["name" => "Dosen"],
                ["name" => "Dokter"],
                ["name" => "Perawat"],
                ["name" => "Tenaga Kesehatan Lainnya"],
                ["name" => "Notaris"],
                ["name" => "Pengacara"],
                ["name" => "Akuntan"],
                ["name" => "Arsitek"],
                ["name" => "Insinyur"],
            ],
        ],
        [
            "name" => "Pekerjaan Non Formal",
            "childs" => [
                ["name" => "Petani"],
                ["name" => "Nelayan"],
                ["name" => "Peternak"],
                ["name" => "Pedagang"],
                ["name" => "Sopir / Pengemudi"],
                ["name" => "Ojek / Pengemudi Roda Dua"],
                ["name" => "Tukang Bangunan"],
                ["name" => "Tukang"],
                ["name" => "Penjahit"],
                ["name" => "Pembantu Rumah Tangga"],
                ["name" => "Pengrajin"],
                ["name" => "Buruh Harian"],
            ],
        ],
        [
            "name" => "Wiraswasta",
            "childs" => [
                ["name" => "Pengusaha Kecil"],
                ["name" => "Pengusaha Menengah"],
                ["name" => "Pengusaha Besar"],
                ["name" => "Usaha Toko"],
                ["name" => "Usaha Kuliner"],
                ["name" => "Usaha Jasa"],
            ],
        ],
        [
            "name" => "Pekerjaan Lainnya",
            "childs" => [
                ["name" => "Seniman"],
                ["name" => "Artis"],
                ["name" => "Atlet"],
                ["name" => "Relawan"],
                ["name" => "Aktivis"],
                ["name" => "Ustaz/Ustazah"],
                ["name" => "Pendeta"],
                ["name" => "Romo"],
                ["name" => "Pemuka Agama Lain"],
                ["name" => "Lainnya"],
            ],
        ],
        [
            "name" => "Tidak Bekerja",
            "childs" => [
                ["name" => "Ibu Rumah Tangga"],
                ["name" => "Pelajar"],
                ["name" => "Mahasiswa"],
                ["name" => "Pensiunan"],
                ["name" => "Tidak Bekerja"],
            ],
        ],
    ];


    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->occupations as $group) {
            $parent = app(config('database.models.PatientOccupation'))->updateOrCreate(
                ['name' => $group['name'], 'parent_id' => null],
                ['name' => $group['name']]
            );

            foreach ($group['childs'] as $child) {
                app(config('database.models.PatientOccupation'))->updateOrCreate(
                    ['name' => $child['name'], 'parent_id' => $parent->id],
                    ['name' => $child['name'], 'parent_id' => $parent->id]
                );
            }
        }
    }
}
