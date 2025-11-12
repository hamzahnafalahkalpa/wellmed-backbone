<?php

use Projects\WellmedBackbone\{
    Contracts, Models, Commands
};

return [
    "namespace"     => "Projects\WellmedBackbone",
    "service_name"  => "WellmedBackbone",
    "paths"         => [
        "local_path"   => 'projects',
        "base_path"    => __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
    ],
    "libs"           => [
        'migration' => 'Database/Migrations',
        'database' => 'Database',
        'model' => 'Models',
        'controller' => 'Controllers',
        'provider' => 'Providers',
        'contract' => 'Contracts',
        'concern' => 'Concerns',
        'command' => 'Commands',
        'route' => 'Routes',
        'observer' => 'Observers',
        'policy' => 'Policies',
        'transformer' => 'Transformers',
        'seeder' => 'Database/Seeders',
        'middleware' => 'Middleware',
        'request' => 'Requests',
        'support' => 'Supports',
        'view' => 'Views',
        'schema' => 'Schemas',
        'facade' => 'Facades',
        'config' => 'Config',
        'import' => 'Imports',
        'data' => 'Data',
        'resource' => 'Resources',
    ],
    "packages" => [
        /*--------------------------------------------------------------------------
        * Note: The contents of the packages are started with the class base name,
        * then followed by config and others. You can be used to override default package config
        * "module-user" => [
        *       "config" => []
        * ]
        *------------------------------------------------------------------------*/
    ],
    "app" => [
        "impersonate" => [
            "storage"   => [
                "driver" => env("FILESYSTEM_DISK", 'local'),
            ],
        ],
        "contracts" => [
        ],
    ],
    "database"     => [
        "models"   => [
        ]
    ],
    "commands" => [
        Commands\AddTenantCommand::class,
        Commands\GenerateCommand::class,
        Commands\ImpersonateCacheCommand::class,
        Commands\ImpersonateMigrateCommand::class,
        Commands\InstallMakeCommand::class,
        Commands\MigrateCommand::class,
        Commands\ModelMakeCommand::class,
        Commands\SeedCommand::class
    ],
    "encodings" => [
    ],
    "provider" => "Projects\WellmedBackbone\\Providers\\WellmedBackboneServiceProvider",
    "packages" => [
        'hanafalah/module-encoding'             => ['provider' => 'Hanafalah\\ModuleEncoding\\ModuleEncodingServiceProvider'],
        'hanafalah/module-regional'             => ['provider' => 'Hanafalah\\ModuleRegional\\ModuleRegionalServiceProvider'],
        'hanafalah/module-user'                 => ['provider' => 'Hanafalah\\ModuleUser\\ModuleUserServiceProvider'],
        'hanafalah/module-workspace'            => ['provider' => 'Hanafalah\\ModuleWorkspace\\ModuleWorkspaceServiceProvider'],
        'hanafalah/module-patient'              => ['provider' => 'Hanafalah\\ModulePatient\\ModulePatientServiceProvider'],
        'hanafalah/module-agent'                => ['provider' => 'Hanafalah\\ModuleAgent\\ModuleAgentServiceProvider'],
        'hanafalah/module-employee'             => ['provider' => 'Hanafalah\\ModuleEmployee\\ModuleEmployeeServiceProvider'],
        'hanafalah/module-funding'              => ['provider' => 'Hanafalah\\ModuleFunding\\ModuleFundingServiceProvider'],
        'hanafalah/module-lab-radiology'        => ['provider' => 'Hanafalah\\ModuleLabRadiology\\ModuleLabRadiologyServiceProvider'],
        'hanafalah/module-medic-service'        => ['provider' => 'Hanafalah\\ModuleMedicService\\ModuleMedicServiceServiceProvider'],
        'hanafalah/module-medical-treatment'    => ['provider' => 'Hanafalah\\ModuleMedicalTreatment\\ModuleMedicalTreatmentServiceProvider'],
        'hanafalah/module-organization'         => ['provider' => 'Hanafalah\\ModuleOrganization\\ModuleOrganizationServiceProvider'],
        'hanafalah/module-payer'                => ['provider' => 'Hanafalah\\ModulePayer\\ModulePayerServiceProvider'],
        'hanafalah/module-payment'              => ['provider' => 'Hanafalah\\ModulePayment\\ModulePaymentServiceProvider'],
        'hanafalah/module-card-identity'        => ['provider' => 'Hanafalah\\ModuleCardIdentity\\ModuleCardIdentityServiceProvider'],
        'hanafalah/module-people'               => ['provider' => 'Hanafalah\\ModulePeople\\ModulePeopleServiceProvider'],
        'hanafalah/module-profession'           => ['provider' => 'Hanafalah\\ModuleProfession\\ModuleProfessionServiceProvider'],
        'hanafalah/module-service'              => ['provider' => 'Hanafalah\\ModuleService\\ModuleServiceServiceProvider'],
        'hanafalah/module-summary'              => ['provider' => 'Hanafalah\\ModuleSummary\\ModuleSummaryServiceProvider'],
        'hanafalah/module-transaction'          => ['provider' => 'Hanafalah\\ModuleTransaction\\ModuleTransactionServiceProvider'],
        'hanafalah/module-treatment'            => ['provider' => 'Hanafalah\\ModuleTreatment\\ModuleTreatmentServiceProvider'],
        'hanafalah/module-warehouse'            => ['provider' => 'Hanafalah\\ModuleWarehouse\\ModuleWarehouseServiceProvider'],
        'hanafalah/module-class-room'           => ['provider' => 'Hanafalah\\ModuleClassRoom\\ModuleClassRoomServiceProvider'],
        'hanafalah/module-mcu'                  => ['provider' => 'Hanafalah\\ModuleMcu\\ModuleMcuServiceProvider'],
        'hanafalah/module-item'                 => ['provider' => 'Hanafalah\\ModuleItem\\ModuleItemServiceProvider'],
        'hanafalah/module-medical-item'         => ['provider' => 'Hanafalah\\ModuleMedicalItem\\ModuleMedicalItemServiceProvider'],
        'hanafalah/module-examination'          => ['provider' => 'Hanafalah\\ModuleExamination\\ModuleExaminationServiceProvider'],
        'hanafalah/module-procurement'          => ['provider' => 'Hanafalah\\ModuleProcurement\\ModuleProcurementServiceProvider'],
        'hanafalah/module-disease'              => ['provider' => 'Hanafalah\\ModuleDisease\\ModuleDiseaseServiceProvider'],
        'hanafalah/module-informed-consent'     => ['provider' => 'Hanafalah\\ModuleInformedConsent\\ModuleInformedConsentServiceProvider'],
        'hanafalah/module-icd'                  => ['provider' => 'Hanafalah\\ModuleIcd\\ModuleIcdServiceProvider'],
        'hanafalah/module-anatomy'              => ['provider' => 'Hanafalah\\ModuleAnatomy\\ModuleAnatomyServiceProvider'],
        'hanafalah/module-physical-examination' => ['provider' => 'Hanafalah\\ModulePhysicalExamination\\ModulePhysicalExaminationServiceProvider'],
        'hanafalah/module-opname-stock'         => ['provider' => 'Hanafalah\\ModuleOpnameStock\\ModuleOpnameStockServiceProvider'],
        'hanafalah/module-appointment'          => ['provider' => 'Hanafalah\\ModuleAppointment\\ModuleAppointmentServiceProvider'],
        'hanafalah/module-distribution'         => ['provider' => 'Hanafalah\\ModuleDistribution\\ModuleDistributionServiceProvider'],
        'hanafalah/module-pharmacy'             => ['provider' => 'Hanafalah\\ModulePharmacy\\ModulePharmacyServiceProvider'],
        'hanafalah/module-event'                => ['provider' => 'Hanafalah\\ModuleEvent\\ModuleEventServiceProvider'],
        'hanafalah/module-manufacture'          => ['provider' => 'Hanafalah\\ModuleManufacture\\ModuleManufactureServiceProvider'],
        'hanafalah/module-handwriting'          => ['provider' => 'Hanafalah\\ModuleHandwriting\\ModuleHandwritingServiceProvider'],
        'hanafalah/module-monitoring'           => ['provider' => 'Hanafalah\\ModuleMonitoring\\ModuleMonitoringServiceProvider'],
        'hanafalah/module-support'              => ['provider' => 'Hanafalah\\ModuleSupport\\ModuleSupportServiceProvider'],
        'hanafalah/module-tax'                  => ['provider' => 'Hanafalah\\ModuleTax\\ModuleTaxServiceProvider'],
        'hanafalah/satu-sehat'                  => ['provider' => 'Hanafalah\\SatuSehat\\SatuSehatServiceProvider'],
        'wellmed-plus/ms-plus-apotek'           => ['provider' => 'WellmedPlus\\MsPlusApotek\\MsPlusApotekServiceProvider'],
        'wellmed-plus/ms-plus-emr'              => ['provider' => 'WellmedPlus\\MsPlusEmr\\MsPlusEmrServiceProvider'],
        'wellmed-plus/ms-plus-hr'               => ['provider' => 'WellmedPlus\\MsPlusHr\\MsPlusHrServiceProvider'],
        'wellmed-plus/ms-plus-point-of-sale'    => ['provider' => 'WellmedPlus\\MsPlusPointOfSale\\MsPlusPointOfSaleServiceProvider'],
        'wellmed-plus/ms-plus-scm'              => ['provider' => 'WellmedPlus\\MsPlusScm\\MsPlusScmServiceProvider']
    ]
];
