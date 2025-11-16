<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModuleEmployee\Data\EmployeeData;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = app(config('database.models.User'))->where('username','admin_plus')->first();
        if (!isset($user)){
            $role_ids   = app(config('database.models.Role'))->where('name','Admin')->get()->pluck('id')->toArray();
            $user       = app(config('database.models.User'))->where('username','admin_plus')->first();
            $profession = app(config('database.models.Profession'))->whereLike('name','Dokter Umum')->firstOrFail();

            request()->merge([
                "card_identity" => [ // Informasi identitas kartu
                    "nip" => null,
                    "bpjs_ketenagakerjaan" => null,
                ],
                "profession_id" => $profession->getKey(), // ID profesi (null jika tidak ada)
                "hired_at" => "2025-03-25", // Tanggal mulai bekerja
                "people" => [ // Informasi individu
                    "id" => null,
                    "name" => "Hamzah",
                    "sex" => "Male", // Pilihan: Male, Female
                    "dob" => "1996-01-01", // Tanggal lahir
                    "pob" => "Pandeglang", // Tempat lahir
                    "last_education_id" => null, // Pendidikan terakhir
                    "marital_status_id" => null, 
                    "total_children" => 10, // Jumlah anak
                    "country_id" => null, // ID negara
                    "address" => [ // Alamat
                        "residence_same_as_ktp" => true, // Apakah domisili sama dengan KTP
                        "ktp" => [
                            "id" => null,
                            "name" => "Banten ya",
                        ],
                        "residence" => [
                            "id" => null,
                            "name" => "Banten ya 2",
                        ],
                    ],
                    "card_identity" => [ // Identitas kartu lainnya
                        "nik" => null,
                        "npwp" => null,
                    ],
                    "family_relationship" => [ // Hubungan keluarga
                        "people_id" => null,
                        "family_role" => [
                            "name" => "Anak"
                        ], // Contoh: Anak, Suami, Istri, dll.
                        "name" => "Fathan",
                        "phone" => "081906521808",
                    ],
                    "phones" => [ // Daftar nomor telepon
                        "08129283746",
                    ]
                ],
                "user_reference" => [ // Referensi user
                    "role_ids" => $role_ids, // Daftar role ID
                    "workspace_type" => 'Tenant',
                    "workspace_id" => tenancy()->tenant->id,
                    "user" => [ // Informasi akun user (boleh null untuk tidak update akun user)
                        "id" => null,
                        "username" => "admin_plus",
                        "password" => "password",
                        "password_confirmation" => "password", // Konfirmasi password
                        "email" => "hamzah_plus@dev.com",
                        "email_verified_at" => now(),
                    ]
                ],
                "profile" => null // Profil (bisa berupa file upload atau path string)
            ]);
            app(config('app.contracts.Employee'))
                ->prepareStoreEmployee($this->requestDTO(EmployeeData::class));
        }
    }
}
