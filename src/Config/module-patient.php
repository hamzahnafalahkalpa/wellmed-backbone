<?php

use Hanafalah\ModulePatient\{
    Commands as ModulePatientCommands,
};

return [
    'practitioner' => 'Employee',
    'payment_detail' => 'PaymentDetail',
    'transaction' => 'PosTransaction',
    'features' => [
        'payer' => false,
        'item_rent' => false
    ],
    'patient_types' => [
        'student' => [
            'schema' => 'PatientPeople',
        ]
    ],
    'filesystem' => [
        'asset_url'     => '/assets/',
        'profile_photo' => 'profiles',
    ]
];