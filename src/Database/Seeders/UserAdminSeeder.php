<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModuleEmployee\Data\EmployeeData;
use Illuminate\Database\Seeder;
use Projects\WellmedBackbone\Jobs\JobRequest;

class UserAdminSeeder extends Seeder
{
    use HasRequest;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $admin = $data['admin'];
        $user = app(config('database.models.User'))->where('username',$admin['username'])->first();
        if (!isset($user)){
            $role_ids   = app(config('database.models.Role'))->where('name','Admin')->get()->pluck('id')->toArray();
            $user = app(config('app.contracts.User'))->prepareStoreUser($this->requestDTO(config('app.contracts.UserData'),[
                "username" => $admin['username'],
                "password" => $admin['password'],
                "password_confirmation" => $admin['password_confirmation'], // Konfirmasi password
                "email" => $admin['email'],
                "email_verified_at" => now(),
                "user_reference" => [
                    "role_ids" => $role_ids, // Daftar role ID
                    "workspace_type" => 'Tenant',
                    "workspace_id" => tenancy()->tenant->id,
                    "reference_type" => 'Employee',
                    'reference' => [
                        'id' => null,
                        'people' => [
                            'name' => $admin['name']
                        ]
                    ]
                ]
            ]));
            $user_reference = $user->userReference;
            $user_reference_id = $user_reference->getKey();
            $now = now();
            app(config('app.contracts.License'))->prepareStoreLicense($this->requestDTO(
                config('app.contracts.LicenseData'),[
                    'reference_type'    => 'Workspace',
                    'reference_id'      => $data['workspace_id'],
                    'expired_at'        => $now->addMonth(),
                    'last_paid'         => $now,
                    'billing_generated_at' => $now,
                    'is_billing_generated' => false,
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
    }
}
