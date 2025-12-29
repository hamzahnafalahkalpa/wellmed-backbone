<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModuleEmployee\Data\EmployeeData;
use Illuminate\Database\Seeder;
use Projects\WellmedBackbone\Jobs\JobRequest;

class LiteEmployeeSeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $user = app(config('database.models.User'))->where('username','admin')->first();
        if (!isset($user)){
            $role_ids   = app(config('database.models.Role'))->where('name','Admin')->get()->pluck('id')->toArray();
            $user       = app(config('database.models.User'))->where('username','admin')->first();
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
                            "name" => "Anak",
                            "label" => "Anak"
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
                        "username" => "admin",
                        "password" => "password",
                        "password_confirmation" => "password", // Konfirmasi password
                        "email" => "hamzah@dev.com",
                        "email_verified_at" => now(),
                    ]
                ],
                "profile" => null // Profil (bisa berupa file upload atau path string)
            ]);
            $employee = app(config('app.contracts.Employee'))
                ->prepareStoreEmployee($this->requestDTO(config('app.contracts.EmployeeData')));
            $user_reference = $employee->userReference;
            $user_reference_id = $user_reference->getKey();
            $now = now();
            app(config('app.contracts.License'))->prepareStoreLicense($this->requestDTO(
                config('app.contracts.LicenseData'),[
                    'reference_type'    => 'Workspace',
                    'reference_id'      => $data['workspace_id'],
                    'expired_at'        => $now->addMonth(),
                    'last_paid'         => $now,
                    'status'            => 'ACTIVE',
                    'recurring_type'    => 'MONTHLY',
                    'flag'              => 'USER_LICENSE',
                    'model_has_license' => [
                        'model_model' => $user_reference,
                        'model_type' => 'UserReference',
                        'model_id'   => $user_reference_id,
                    ]
                ]
            ));

        }

        $medic_service = app(config('database.models.MedicService'))->withoutGlobalScopes()->where('label','UMUM')->first();
        $medic_service->is_restricted = false;
        $medic_service->save();

        $workspace = app(config('database.models.Workspace'))->findOrFail($data['workspace_id']);
        $building = app(config('database.models.Building'))->first();
        $room_payload = [
            "name" => "Ruang ".$medic_service->name,
            "floor"=> 1,
            "phone"=> null,
            "medic_service_id"=> $medic_service->getKey(), //nullable, GET FROM SETTING > FASKES SERVICE > MEDICAL SERVICE
            'medic_service_model' => $medic_service,
            "building_id"=> $building->getKey(),
            "ihs_number" => $workspace->integration['satu_sehat']['general']['ihs_number'] ?? null
        ];
        $room_model = app(config('app.contracts.Room'))->prepareStoreRoom(
            $this->requestDTO(config('app.contracts.RoomData'), $room_payload)
        );
    }
}
