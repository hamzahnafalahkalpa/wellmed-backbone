<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\ModuleMedicService\Enums\Label;   
use Illuminate\Database\Seeder;

class MedicServiceSeeder extends Seeder
{
    use HasRequestData;

    public function run()
    {
        $arr = [
            [
                'name' => 'Rawat Jalan',
                'flag' => 'MedicService','label' => Label::OUTPATIENT->value,
                'childs' => [
                    ['name' => 'Umum', 'flag' => 'MedicService','label' => 'UMUM'],
                    ['name' => 'Orthopedi', 'flag' => 'MedicService','label' => 'ORTHOPEDI'],
                    ['name' => 'Sunat', 'flag' => 'MedicService','label' => 'SUNAT'],
                    ['name' => 'Kecantikan', 'flag' => 'MedicService','label' => 'KECANTIKAN'],
                    ['name' => 'Mata', 'flag' => 'MedicService','label' => 'MATA'],
                    ['name' => 'THT', 'flag' => 'MedicService','label' => 'THT'],
                    ['name' => 'Penyakit Dalam', 'flag' => 'MedicService','label' => 'INTERNIS'],
                    ['name' => 'Gigi & Mulut', 'flag' => 'MedicService','label' => 'GIGI & MULUT'],
                    ['name' => 'KIA', 'flag' => 'MedicService','label' => 'KIA'],
                    ['name' => 'Lansia', 'flag' => 'MedicService','label' => 'LANSIA'],
                    ['name' => 'Admin', 'flag' => 'MedicService','label' => 'ADMIN'],
                    ['name' => 'Vaccine', 'flag' => 'MedicService','label' => 'VACCINE'],
                    ['name' => 'MTBS', 'flag' => 'MedicService','label' => 'MTBS']
                ]
            ],
            [
                'name' => 'Laboratorium Klinik',
                'flag' => 'MedicService','label' => Label::LABORATORY->value,
                'childs' => [
                    ['name' => 'Patalogi Klinik', 'flag' => 'MedicService','label' => 'PATOLOGI KLINIK'],
                    ['name' => 'Patalogi Anatomi', 'flag' => 'MedicService','label' => 'PATOLOGI ANATOMI'],
                ]
            ],
            ['name' => 'Medical Check Up', 'flag' => 'MedicService','label' => Label::MCU->value],
            ['name' => 'Ruang Tindakan', 'flag' => 'MedicService','label' => Label::TREATMENT_ROOM->value],
            ['name' => 'Radiologi', 'flag' => 'MedicService','label' => Label::RADIOLOGY->value],
            ['name' => 'Rawat Inap', 'flag' => 'MedicService','label' => Label::INPATIENT->value],
            ['name' => 'Administrasi', 'flag' => 'MedicService','label' => Label::ADMINISTRATION->value],
            [
                'name' => 'Kefarmasian',
                'flag' => 'MedicService',
                'label' => Label::PHARMACY->value,
                'childs' => [
                    ['name' => 'Instalasi Farmasi', 'flag' => 'MedicService','label' => Label::PHARMACY_UNIT->value],
                    ['name' => 'Gudang Farmasi', 'flag' => 'MedicService','label' => Label::PHARMACY->value],
                ]
            ],
            ['name' => 'Persalinan', 'flag' => 'MedicService','label' => Label::VERLOS_KAMER->value],
            ['name' => 'Instalasi Gawat Darurat', 'flag' => 'MedicService','label' => Label::EMERGENCY_UNIT->value],
            ['name' => 'Puskesmas Pembantu', 'flag' => 'MedicService','label' => Label::PUSKESMAS_PEMBANTU->value],
            ['name' => 'Posyandu', 'flag' => 'MedicService','label' => Label::POSYANDU->value],
            ['name' => 'Surveillance', 'flag' => 'MedicService','label' => Label::SURVEILLANCE->value],
        ];
        foreach ($arr as $data) {
            app(config('app.contracts.MedicService'))->prepareStoreMedicService($this->requestDTO(config('app.contracts.MedicServiceData'),$data));
        }
    }
}
