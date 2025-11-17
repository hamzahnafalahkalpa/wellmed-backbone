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
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $arr = [
            [
                'name' => 'Rawat Jalan',
                'flag' => 'MedicService','is_restricted' => false,'label' => Label::OUTPATIENT->value,
                'childs' => [
                    ['name' => 'Umum', 'flag' => 'MedicService','is_restricted' => false,'label' => 'UMUM'],
                    ['name' => 'Orthopedi', 'flag' => 'MedicService','is_restricted' => false,'label' => 'ORTHOPEDI'],
                    ['name' => 'Sunat', 'flag' => 'MedicService','is_restricted' => false,'label' => 'SUNAT'],
                    ['name' => 'Kecantikan', 'flag' => 'MedicService','is_restricted' => false,'label' => 'KECANTIKAN'],
                    ['name' => 'Mata', 'flag' => 'MedicService','is_restricted' => false,'label' => 'MATA'],
                    ['name' => 'THT', 'flag' => 'MedicService','is_restricted' => false,'label' => 'THT'],
                    ['name' => 'Penyakit Dalam', 'flag' => 'MedicService','is_restricted' => false,'label' => 'INTERNIS'],
                    ['name' => 'Gigi & Mulut', 'flag' => 'MedicService','is_restricted' => false,'label' => 'GIGI & MULUT'],
                    ['name' => 'KIA', 'flag' => 'MedicService','is_restricted' => false,'label' => 'KIA'],
                    ['name' => 'Lansia', 'flag' => 'MedicService','is_restricted' => false,'label' => 'LANSIA'],
                    ['name' => 'Admin', 'flag' => 'MedicService','is_restricted' => false,'label' => 'ADMIN'],
                    ['name' => 'Vaccine', 'flag' => 'MedicService','is_restricted' => false,'label' => 'VACCINE'],
                    ['name' => 'MTBS', 'flag' => 'MedicService','is_restricted' => false,'label' => 'MTBS']
                ]
            ],
            [
                'name' => 'Laboratorium Klinik',
                'flag' => 'MedicService','is_restricted' => false,'label' => Label::LABORATORY->value,
                'childs' => [
                    ['name' => 'Patalogi Klinik', 'flag' => 'MedicService','is_restricted' => false,'label' => 'PATOLOGI KLINIK'],
                    ['name' => 'Patalogi Anatomi', 'flag' => 'MedicService','is_restricted' => false,'label' => 'PATOLOGI ANATOMI'],
                ]
            ],
            ['name' => 'Medical Check Up', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::MCU->value],
            ['name' => 'Ruang Tindakan', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::TREATMENT_ROOM->value],
            ['name' => 'Radiologi', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::RADIOLOGY->value],
            ['name' => 'Rawat Inap', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::INPATIENT->value],
            ['name' => 'Administrasi', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::ADMINISTRATION->value],
            [
                'name' => 'Kefarmasian',
                'flag' => 'MedicService',
                'is_restricted' => false, 'label' => Label::PHARMACY->value,
                'childs' => [
                    ['name' => 'Instalasi Farmasi', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::PHARMACY_UNIT->value],
                    ['name' => 'Gudang Farmasi', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::PHARMACY->value],
                ]
            ],
            ['name' => 'Persalinan', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::VERLOS_KAMER->value],
            ['name' => 'Instalasi Gawat Darurat', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::EMERGENCY_UNIT->value],
            ['name' => 'Puskesmas Pembantu', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::PUSKESMAS_PEMBANTU->value],
            ['name' => 'Posyandu', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::POSYANDU->value],
            ['name' => 'Surveillance', 'flag' => 'MedicService','is_restricted' => false,'label' => Label::SURVEILLANCE->value],
        ];
        foreach ($arr as $data) {
            app(config('app.contracts.MedicService'))->prepareStoreMedicService($this->requestDTO(config('app.contracts.MedicServiceData'),$data));
        }
    }
}
