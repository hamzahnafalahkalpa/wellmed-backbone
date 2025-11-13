<?php

namespace Hanafalah\KlinikStarterpack\Database\Seeders;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeData;
use Illuminate\Database\Seeder;

class PatientTypeSeeder extends Seeder
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
                'flag'  => 'PatientType',
                'label' => 'UMUM',
                'is_delete_able' => true
            ],
            [
                'name'  => 'Karyawan',
                'flag'  => 'PatientType',
                'label' => 'KARYAWAN',
                'is_delete_able' => true
            ],
            [
                'name'  => 'Member',
                'flag'  => 'PatientType',
                'label' => 'MEMBER',
                'is_delete_able' => true
            ],
            [
                'name'  => 'Crew',
                'flag'  => 'PatientType',
                'label' => 'CREW',
                'is_delete_able' => true
            ],
            [
                'name'  => 'Partner',
                'flag'  => 'PatientType',
                'label' => 'PARTNER',
                'is_delete_able' => true
            ],
            [
                'name'  => 'Unidentified',
                'flag'  => 'PatientType',
                'label' => 'UNIDENTIFIED',
                'is_delete_able' => false
            ]
        ];
        foreach ($arr as $data) {
            app(config('app.contracts.PatientType'))->prepareStorePatientType($this->requestDTO(PatientTypeData::class,$data));
        }
    }
}
