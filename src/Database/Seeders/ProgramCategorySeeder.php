<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModuleEvent\Contracts\Data\ProgramCategoryData;
use Illuminate\Database\Seeder;

class ProgramCategorySeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $arr = [
            ["label" => "Promosi Kesehatan dan Pemberdayaan Masyarakat","name" => "Promosi Kesehatan dan Pemberdayaan Masyarakat"],
            ["label" => "Kesehatan Ibu","name" => "Kesehatan Ibu"],
            ["label" => "Kesehatan Anak","name" => "Kesehatan Anak"],
            ["label" => "Kesehatan Remaja dan Usia Produktif","name" => "Kesehatan Remaja dan Usia Produktif"],
            ["label" => "Kesehatan Lansia","name" => "Kesehatan Lansia"],
            ["label" => "Kesehatan Gizi","name" => "Kesehatan Gizi"],
            ["label" => "Kesehatan Lingkungan","name" => "Kesehatan Lingkungan"],
            ["label" => "Pencegahan dan Pengendalian Penyakit Menular","name" => "Pencegahan dan Pengendalian Penyakit Menular"],
            ["label" => "Pencegahan dan Pengendalian Penyakit Tidak Menular","name" => "Pencegahan dan Pengendalian Penyakit Tidak Menular"],
            ["label" => "Pelayanan Kesehatan Tradisional","name" => "Pelayanan Kesehatan Tradisional"],
            ["label" => "Kesehatan Jiwa","name" => "Kesehatan Jiwa"],
            ["label" => "Kesehatan Indera","name" => "Kesehatan Indera (Mata, Telinga)"],
            ["label" => "Kesehatan Kerja","name" => "Kesehatan Kerja"],
            ["label" => "Kesehatan Gigi dan Mulut","name" => "Kesehatan Gigi dan Mulut"],
            ["label" => "Kesehatan Sekolah","name" => "Kesehatan Sekolah (UKS)"],
            ["label" => "Kesehatan Olahraga","name" => "Kesehatan Olahraga"],
            ["label" => "Program Kefarmasian dan Alkes","name" => "Program Kefarmasian dan Alkes"],
            ["label" => "Program Laboratorium","name" => "Program Laboratorium"],
            ["label" => "Program Imunisasi","name" => "Program Imunisasi"],
            ["label" => "Manajemen Puskesmas dan Sistem Informas","name" => "Manajemen Puskesmas dan Sistem Informasi"]
        ];
        foreach ($arr as $data) {
            $data['flag'] = 'ProgramCategory';
            app(config('app.contracts.ProgramCategory'))->prepareStoreProgramCategory(
                $this->requestDTO(ProgramCategoryData::class,$data)
            );
        }
    }
}