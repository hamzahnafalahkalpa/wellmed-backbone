<?php

use Hanafalah\ModulePatient\{
    Commands as ModulePatientCommands,
};
use Projects\WellmedBackbone\Exports\VisitRegistrationEmrExport;
use Projects\WellmedBackbone\Imports\Patient;

return [
    'direct_referral_froms' => [
        //DIRECT REFERRAL FROM POLY
        'RADIOLOGY','PATOLOGI KLINIK','PATOLOGI ANATOMI'
    ],
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
        'profile_photo' => '',
    ],
    'imports' => [
        'Patient' => Patient::class
    ],
    'exports' => [
        'VisitRegistrationEmr' => VisitRegistrationEmrExport::class,
    ],
];