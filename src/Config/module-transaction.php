<?php

return [
    'survey_flag_key' => 'label',
    'warehouse' => 'Room',
    'payment_summary' => 'PaymentSummary',
    'payment_detail' => 'PaymentDetail',
    'consument' => 'Consument',
    'transaction_types'=> [
        //THIS KEY SAME WITH MODEL NAME USING SNAKE CASE
        'submission' => [
            'schema' => 'Submission',
        ],
        'pharmacy_sale' => [
            'schema' => 'PharmacySale',
        ],
        'visit_patient' => [
            'schema' => 'VisitPatient',
        ]
    ],
];