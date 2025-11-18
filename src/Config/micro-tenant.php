<?php

use Projects\WellmedBackbone\Contracts\Supports\ConnectionManager;

return [
    'dev_mode'     => false,
    'direct_provider_access' => true,
    'database' => [
        'connection_manager' => ConnectionManager::class,
        'app_tenant'   => [
            'prefix' => env('BACKBONE_DATABASE_PREFIX', 'app_'),
            'suffix' => ''
        ],
        'laravel-support' => [
            'service_cache'  => \Projects\WellmedBackbone\Contracts\Supports\ServiceCache::class,
        ],
        'model_connections' => [
            'central'        => [
                'models' => [
                    'ApiAccess',
                    'Cache',
                    'CacheLock',
                    'Country',
                    'District',
                    'Domain',
                    'FailedJob',
                    'JobBatch',
                    'Job',
                    'PasswordResetToken',
                    'PayloadMonitoring',
                    'PersonalAccessToken',
                    'Province',
                    'Subdistrict',
                    'Tenant',
                    'UserReference',
                    'User',
                    'Village',
                    'Workspace',
                    'WellmedAddress',
                    'MasterFeature',
                    'InstalledFeature',
                    'MasterFeature',
                    'WellmedUnicode',
                    'WellmedEncoding',
                    'WellmedModelHasEncoding',
                    'Version',
                    'ModelHasFeature',
                    'WellmedPermission',
                    'Disease',
                    'RestrictionFeature',
                    'Timezone'
                ]
            ],
            'central_app'    => [
                'models' => [
                    'CentralActivityStatus',
                    'CentralActivity'
                ]
            ],
            'central_tenant' => [
                'models' => [

                ]
            ],
            'pos' => [
                'is_cluster' => true,
                'connection_as' => 'tenant',
                'models' => [
                    'Transaction',
                    'PosTransaction',
                    'PosTransactionItem',
                    'Activity',
                    'ActivityStatus',
                    'Billing',
                    'Invoice',
                    'TransactionHasConsument',
                    'PosTransactionItem',
                    'PaymentSummary',
                    'PaymentHistory',
                    'SplitPayment',
                    'PaymentDetail',
                    'PaymentHasModel',
                    'Refund',
                    'WalletTransaction'
                ]
            ],
            'emr' => [
                'is_cluster' => true,
                'connection_as' => 'tenant',
                'models' => [
                    'VisitPatient',
                    'VisitRegistration',
                    'Referral',
                    'VisitExamination',
                    'PatientTypeHistory',
                    'PractitionerEvaluation',
                    'ExaminationSummary',
                    'Support',
                    'InformedConsent',
                    'Assessment',
                    'Diagnose',
                    'PatientIllness',
                    'ExaminationTreatment',
                    'Prescription'
                ]
            ],
            'scm' => [
                'is_cluster' => true,
                'connection_as' => 'tenant',
                'models' => [
                    'OpnameStock',
                    'Procurement',
                    'PurchaseRequest',
                    'Purchasing',
                    'PurchaseOrder',
                    'ReceiveOrder',
                    'Distribution',
                    'WorkOrder',
                    'ProcurementService',
                    'ProcurementList',
                    'CardStock'
                ]
            ]
        ]
    ],
];