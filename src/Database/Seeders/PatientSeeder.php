<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    use HasRequestData;

    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     echo "[DEBUG] Booting ".class_basename($this)."\n";

    //     $umum = app(config('database.models.MedicService'))->where('label', 'Umum')->first();
    //     for ($i = 1; $i <= 1000; $i++) {
    //         $data = [
    //             "id"=> null,
    //             "card_identity"=> [
    //                 "old_mr"=> null,
    //                 "ihs_number"=> null,
    //                 "bpjs"=> null
    //             ],
    //             "people"=> [
    //                 "first_name"=> "Hana",
    //                 "last_name"=> "Diana Ivankov",
    //                 "email"=> null,
    //                 "phone_1"=> "0822123293948",
    //                 "phone_2"=> null,
    //                 "blood_type"=> "O", //nullable, in=>A,B,AB,O,A-,B-,AB-,O-,A+,B+,AB+,O+
    //                 "pob"=> "Bandung",
    //                 "dob"=> "1991-05-03",
    //                 "sex"=> "Male", 
    //                 "address"=> [
    //                     "ktp"=> [
    //                         "name"=> "Jl.Kepatihan Gg.Warung Bata No.24",
    //                         "rt"=> "012",
    //                         "rw"=> "008",
    //                         "zip_code"=> "45117",
    //                         "village" => [
    //                             "id"=> 1
    //                         ]
    //                     ],
    //                     "residence"=> [
    //                         "name"=> "Jl.Kepatihan Gg.Warung Bata No.24",
    //                         "rt"=> "012",
    //                         "rw"=> "008",
    //                         "zip_code"=> "45117",
    //                         "village" => [
    //                             "id"=> 1
    //                         ],
    //                         "same_as_ktp"=> true
    //                     ]
    //                 ],
    //                 "card_identity"=> [
    //                     "nik"=> "3600120305910099",
    //                     "sim"=> null,
    //                     "passport"=> "13214231423143241",
    //                     "kk"=> null
    //                 ]
    //             ],
    //             "visit_examination"=> [ //required for rawat jalan
    //                 "id"=> null,
    //                 "medic_service_id" => $umum->getKey(), //required, GET AUTOLIST - MEDIC SERVICE LIST
    //             ]
    //         ];
    //         app(config('app.contracts.Patient'))->prepareStorePatient($this->requestDTO(config('app.contracts.PatientData'), $data));
    //     }
    // }

    public function run(): void{
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $patient = app(config('database.models.Patient'))->find('01kd1f9q4wtvq8sjbjgxj8wcng');
        if (!isset($patient)){
            $sqlFile = __DIR__ . '/data/patient_seeder.sql';
            $sql = file_get_contents($sqlFile);
            \DB::unprepared($sql);
        }

        // $faker = \Faker\Factory::create('id_ID');

        // $umum = app(config('database.models.Wallet'))
        //     // ->where('label', 'UMUM')
        //     ->withoutGlobalScopes()
        //     ->get();
        // for ($i = 1; $i <= 1000; $i++) {

        //     $gender = $faker->randomElement(['Male', 'Female']);
        //     $firstName = $gender === 'Male'
        //         ? $faker->firstNameMale
        //         : $faker->firstNameFemale;

        //     $lastName = $faker->lastName;

        //     $nik = $faker->numerify('32##############');
        //     $phone = '08' . $faker->numerify('##########');

        //     $data = [
        //         "id" => null,
        //         "card_identity" => [
        //             "old_mr" => null,
        //             "ihs_number" => null,
        //             "bpjs" => $faker->optional()->numerify('0000000000000'),
        //         ],
        //         'reference_type' => 'People',
        //         "reference" => [
        //             "first_name" => $firstName,
        //             "last_name"  => $lastName,
        //             "email"      => $faker->optional()->safeEmail,
        //             "phone_1"    => $phone,
        //             "phone_2"    => $faker->optional()->phoneNumber,
        //             "blood_type" => $faker->randomElement([
        //                 'A','B','AB','O','A+','B+','AB+','O+','A-','B-','AB-','O-'
        //             ]),
        //             "pob" => $faker->city,
        //             "dob" => $faker->date('Y-m-d', '-18 years'),
        //             "sex" => $gender,
        //             "address" => [
        //                 "ktp" => [
        //                     "name" => $faker->streetAddress,
        //                     "rt"   => str_pad((string) rand(1, 20), 3, '0', STR_PAD_LEFT),
        //                     "rw"   => str_pad((string) rand(1, 20), 3, '0', STR_PAD_LEFT),
        //                     "zip_code" => $faker->postcode,
        //                     "village" => [
        //                         "id" => rand(1, 10),
        //                     ],
        //                 ],
        //                 "residence" => [
        //                     "name" => $faker->streetAddress,
        //                     "rt"   => str_pad((string) rand(1, 20), 3, '0', STR_PAD_LEFT),
        //                     "rw"   => str_pad((string) rand(1, 20), 3, '0', STR_PAD_LEFT),
        //                     "zip_code" => $faker->postcode,
        //                     "village" => [
        //                         "id" => rand(1, 10),
        //                     ],
        //                     "same_as_ktp" => false,
        //                 ],
        //             ],
        //             "card_identity" => [
        //                 "nik"      => $nik,
        //                 "sim"      => $faker->optional()->numerify('SIM########'),
        //                 "passport" => $faker->optional()->bothify('P########'),
        //                 "kk"       => $faker->optional()->numerify('32##############'),
        //             ],
        //         ]

        //         // "visit_examination" => [
        //         //     "id" => null,
        //         //     "medic_service_id" => $umum->getKey(),
        //         // ],
        //     ];

        //     app(config('app.contracts.Patient'))
        //         ->prepareStorePatient(
        //             $this->requestDTO(config('app.contracts.PatientData'), $data)
        //         );
        // }
    }

}
