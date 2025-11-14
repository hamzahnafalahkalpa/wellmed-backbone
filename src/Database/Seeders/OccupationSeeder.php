<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleProfession\Contracts\Data\OccupationData;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    use HasRequestData;

    public $occupations = [
        ["name" => "Kepala Klinik", "label" => "Kepala Klinik"],
        ["name" => "Kepala Perawat", "label" => "Kepala Perawat"],
        ["name" => "Kepala Bidan", "label" => "Kepala Bidan"],
        ["name" => "Kepala Apotek", "label" => "Kepala Apotek"],
        ["name" => "Kepala Laboratorium", "label" => "Kepala Laboratorium"],
        ["name" => "Kepala Gizi", "label" => "Kepala Gizi"],
        ["name" => "Kepala Administrasi", "label" => "Kepala Administrasi"],
        ["name" => "Kepala Keuangan", "label" => "Kepala Keuangan"],
        ["name" => "Kepala Kesehatan Lingkungan", "label" => "Kepala Kesehatan Lingkungan"],
        ["name" => "Kepala IT", "label" => "Kepala IT"], // opsional jika ada unit IT
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->occupations as $group) {
            $group['flag'] = 'Occupation';
            app(config('app.contracts.Occupation'))->prepareStoreOccupation(
                $this->requestDTO(OccupationData::class, $group)
            );
        }
    }
}
