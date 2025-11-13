<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeServiceData;
use Illuminate\Database\Seeder;

class PatientTypeServiceSeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $arr = [
            [
                'name'  => 'Umum',
                'flag'  => 'PatientTypeService',
                'label' => 'UMUM'
            ],
            [
                'name'  => 'BPJS Kelas I',
                'flag'  => 'PatientTypeService',
                'label' => 'BPJS'
            ],
            [
                'name'  => 'BPJS Kelas II',
                'flag'  => 'PatientTypeService',
                'label' => 'BPJS'
            ],
            [
                'name'  => 'BPJS Kelas III',
                'flag'  => 'PatientTypeService',
                'label' => 'BPJS'
            ],
            [
                'name'  => 'Asuransi',
                'flag'  => 'PatientTypeService',
                'label' => 'ASURANSI'
            ]
        ];
        foreach ($arr as $data) {
            app(config('app.contracts.PatientTypeService'))->prepareStorePatientTypeService($this->requestDTO(PatientTypeServiceData::class,$data));
        }
    }
}
