<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleProfession\Contracts\Data\ProfessionData;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    use HasRequestData;

    public $professions = [
        [
            "name" => "Tenaga Medis",
            "childs" => [
                ["name" => "Dokter Umum", 'label' => 'Doctor'],
                ["name" => "Dokter Spesialis", 'label' => 'Doctor'],
                ["name" => "Dokter Gigi Umum", 'label' => 'Doctor'],
                ["name" => "Dokter Gigi Spesialis", 'label' => 'Doctor'],
            ],
        ],
        [
            "name" => "Tenaga Keperawatan",
            "childs" => [
                ["name" => "Perawat", 'label' => 'Nurse'],
            ],
        ],
        [
            "name" => "Tenaga Kebidanan",
            "childs" => [
                ["name" => "Bidan", 'label' => 'Midfwife'],
            ],
        ],
        [
            "name" => "Tenaga Kefarmasian",
            "childs" => [
                ["name" => "Apoteker",'label' => 'Pharmacy'],
                ["name" => "Tenaga Teknik Kefarmasian",'label' => 'Pharmacy'],
            ],
        ],
        [
            "name" => "Tenaga Kesehatan Masyarakat",
            "childs" => [
                ["name" => "Epidmiolog Kesehatan"],
                ["name" => "Tenaga Promosi Kesehatan Dan Ilmu Perilaku"],
                ["name" => "Tenaga Kesehatan Reproduksi Dan Keluarga"],
                ["name" => "Tenaga Biostatistik Dan Kependudukan"],
                ["name" => "Tenaga Administrasi Dan Kebijakan Kesehatan"],
                ["name" => "Pembimbing Kesehatan Kerja"],
            ],
        ],
        [
            "name" => "Tenaga Kesehatan Lingkungan",
            "childs" => [
                ["name" => "Tenaga Sanitasi Lingkungan"],
                ["name" => "Entomolog Kesehatan"],
                ["name" => "Mikrobiolog Kesehatan"],
            ],
        ],
        [
            "name" => "Tenaga Keterapian Fisik",
            "childs" => [
                ["name" => "Fisioterapis"],
                ["name" => "Okupasi Terapis"],
                ["name" => "Terapis Wicara"],
            ],
        ],
        [
            "name" => "Tenaga Gizi",
            "childs" => [
                ["name" => "Nutrisionis"],
                ["name" => "Dietisien"],
            ],
        ],
        [
            "name" => "Tenaga Teknik Biomedika",
            "childs" => [
                ["name" => "Perekam Medis Dan Informasi Kesehatan"],
                ["name" => "Penata Anestesi"],
                ["name" => "Teknik Kardiovaskuler"],
                ["name" => "Teknisi Pelayanan Darah"],
                ["name" => "Teknisi Gigi"],
                ["name" => "Terapis Gigi Dan Mulut"],
                ["name" => "Audiologis"],
                ["name" => "Refraksionis Optisien/Optometris"],
            ],
        ],
        [
            "name" => "Tenaga Keterapian Medis",
            "childs" => [
                ["name" => "Elektromedis"],
                ["name" => "Fisikawan Medik"],
                ["name" => "Laboratorium Medik"],
                ["name" => "Radiografer"],
                ["name" => "Radioterapis"],
                ["name" => "Ortotik Prostetik"],
            ],
        ],
        [
            "name" => "Tenaga Kesehatan Tradisional",
            "childs" => [
                ["name" => "Tenaga Kesehatan Tradisional Ramuan"],
                ["name" => "Tenaga Kesehatan Tradisional Keterampilan"],
                ["name" => "Akupuntur"],
            ],
        ],
        [
            "name" => "Tenaga Non Kesehatan",
            "childs" => [
                ["name" => "Administrasi"],
                ["name" => "Bagian Prasarana Atau Gudang Inventaris"],
                ["name" => "Cleaning Service"],
                ["name" => "Finance"],
                ["name" => "Human Resourch Development"],
                ["name" => "Keamanan"],
                ["name" => "Manager"],
                ["name" => "Operator"],
            ],
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        foreach ($this->professions as $group) {
            $group['flag'] = 'Profession';
            app(config('app.contracts.Profession'))->prepareStoreProfession($this->requestDTO(ProfessionData::class,$group));
        }
    }
}
