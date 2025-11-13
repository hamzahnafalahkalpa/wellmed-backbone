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
        'hanafalah/module-encoding'             => ['repository' => 'hamzahnafalahkalpa/module-encoding','provider' => 'Hanafalah\\ModuleEncoding\\ModuleEncodingServiceProvider'],
        'hanafalah/module-regional'             => ['repository' => 'hamzahnafalahkalpa/module-regional','provider' => 'Hanafalah\\ModuleRegional\\ModuleRegionalServiceProvider'],
        'hanafalah/module-user'                 => ['repository' => 'hamzahnafalahkalpa/module-user','provider' => 'Hanafalah\\ModuleUser\\ModuleUserServiceProvider'],
        'hanafalah/module-workspace'            => ['repository' => 'hamzahnafalahkalpa/module-workspace','provider' => 'Hanafalah\\ModuleWorkspace\\ModuleWorkspaceServiceProvider'],
        'hanafalah/module-patient'              => ['repository' => 'hamzahnafalahkalpa/module-patient','provider' => 'Hanafalah\\ModulePatient\\ModulePatientServiceProvider'],
        'hanafalah/module-agent'                => ['repository' => 'hamzahnafalahkalpa/module-agent','provider' => 'Hanafalah\\ModuleAgent\\ModuleAgentServiceProvider'],
        'hanafalah/module-employee'             => ['repository' => 'hamzahnafalahkalpa/module-employee','provider' => 'Hanafalah\\ModuleEmployee\\ModuleEmployeeServiceProvider'],
        'hanafalah/module-funding'              => ['repository' => 'hamzahnafalahkalpa/module-funding','provider' => 'Hanafalah\\ModuleFunding\\ModuleFundingServiceProvider'],
        'hanafalah/module-lab-radiology'        => ['repository' => 'hamzahnafalahkalpa/module-lab-radiology','provider' => 'Hanafalah\\ModuleLabRadiology\\ModuleLabRadiologyServiceProvider'],
        'hanafalah/module-medic-service'        => ['repository' => 'hamzahnafalahkalpa/module-medic-service','provider' => 'Hanafalah\\ModuleMedicService\\ModuleMedicServiceServiceProvider'],
        'hanafalah/module-medical-treatment'    => ['repository' => 'hamzahnafalahkalpa/module-medical-treatment','provider' => 'Hanafalah\\ModuleMedicalTreatment\\ModuleMedicalTreatmentServiceProvider'],
        'hanafalah/module-organization'         => ['repository' => 'hamzahnafalahkalpa/module-organization','provider' => 'Hanafalah\\ModuleOrganization\\ModuleOrganizationServiceProvider'],
        'hanafalah/module-payer'                => ['repository' => 'hamzahnafalahkalpa/module-payer','provider' => 'Hanafalah\\ModulePayer\\ModulePayerServiceProvider'],
        'hanafalah/module-payment'              => ['repository' => 'hamzahnafalahkalpa/module-payment','provider' => 'Hanafalah\\ModulePayment\\ModulePaymentServiceProvider'],
        'hanafalah/module-card-identity'        => ['repository' => 'hamzahnafalahkalpa/module-card-identity','provider' => 'Hanafalah\\ModuleCardIdentity\\ModuleCardIdentityServiceProvider'],
        'hanafalah/module-people'               => ['repository' => 'hamzahnafalahkalpa/module-people','provider' => 'Hanafalah\\ModulePeople\\ModulePeopleServiceProvider'],
        'hanafalah/module-profession'           => ['repository' => 'hamzahnafalahkalpa/module-profession','provider' => 'Hanafalah\\ModuleProfession\\ModuleProfessionServiceProvider'],
        'hanafalah/module-service'              => ['repository' => 'hamzahnafalahkalpa/module-service','provider' => 'Hanafalah\\ModuleService\\ModuleServiceServiceProvider'],
        'hanafalah/module-summary'              => ['repository' => 'hamzahnafalahkalpa/module-summary','provider' => 'Hanafalah\\ModuleSummary\\ModuleSummaryServiceProvider'],
        'hanafalah/module-transaction'          => ['repository' => 'hamzahnafalahkalpa/module-transaction','provider' => 'Hanafalah\\ModuleTransaction\\ModuleTransactionServiceProvider'],
        'hanafalah/module-treatment'            => ['repository' => 'hamzahnafalahkalpa/module-treatment','provider' => 'Hanafalah\\ModuleTreatment\\ModuleTreatmentServiceProvider'],
        'hanafalah/module-warehouse'            => ['repository' => 'hamzahnafalahkalpa/module-warehouse','provider' => 'Hanafalah\\ModuleWarehouse\\ModuleWarehouseServiceProvider'],
        'hanafalah/module-class-room'           => ['repository' => 'hamzahnafalahkalpa/module-class-room','provider' => 'Hanafalah\\ModuleClassRoom\\ModuleClassRoomServiceProvider'],
        'hanafalah/module-mcu'                  => ['repository' => 'hamzahnafalahkalpa/module-mcu','provider' => 'Hanafalah\\ModuleMcu\\ModuleMcuServiceProvider'],
        'hanafalah/module-item'                 => ['repository' => 'hamzahnafalahkalpa/module-item','provider' => 'Hanafalah\\ModuleItem\\ModuleItemServiceProvider'],
        'hanafalah/module-medical-item'         => ['repository' => 'hamzahnafalahkalpa/module-medical-item','provider' => 'Hanafalah\\ModuleMedicalItem\\ModuleMedicalItemServiceProvider'],
        'hanafalah/module-examination'          => ['repository' => 'hamzahnafalahkalpa/module-examination','provider' => 'Hanafalah\\ModuleExamination\\ModuleExaminationServiceProvider'],
        'hanafalah/module-procurement'          => ['repository' => 'hamzahnafalahkalpa/module-procurement','provider' => 'Hanafalah\\ModuleProcurement\\ModuleProcurementServiceProvider'],
        'hanafalah/module-disease'              => ['repository' => 'hamzahnafalahkalpa/module-disease','provider' => 'Hanafalah\\ModuleDisease\\ModuleDiseaseServiceProvider'],
        'hanafalah/module-informed-consent'     => ['repository' => 'hamzahnafalahkalpa/module-informed-consent','provider' => 'Hanafalah\\ModuleInformedConsent\\ModuleInformedConsentServiceProvider'],
        'hanafalah/module-icd'                  => ['repository' => 'hamzahnafalahkalpa/module-icd','provider' => 'Hanafalah\\ModuleIcd\\ModuleIcdServiceProvider'],
        'hanafalah/module-anatomy'              => ['repository' => 'hamzahnafalahkalpa/module-anatomy','provider' => 'Hanafalah\\ModuleAnatomy\\ModuleAnatomyServiceProvider'],
        'hanafalah/module-physical-examination' => ['repository' => 'hamzahnafalahkalpa/module-physical-examination','provider' => 'Hanafalah\\ModulePhysicalExamination\\ModulePhysicalExaminationServiceProvider'],
        'hanafalah/module-opname-stock'         => ['repository' => 'hamzahnafalahkalpa/module-opname-stock','provider' => 'Hanafalah\\ModuleOpnameStock\\ModuleOpnameStockServiceProvider'],
        'hanafalah/module-appointment'          => ['repository' => 'hamzahnafalahkalpa/module-appointment','provider' => 'Hanafalah\\ModuleAppointment\\ModuleAppointmentServiceProvider'],
        'hanafalah/module-distribution'         => ['repository' => 'hamzahnafalahkalpa/module-distribution','provider' => 'Hanafalah\\ModuleDistribution\\ModuleDistributionServiceProvider'],
        'hanafalah/module-pharmacy'             => ['repository' => 'hamzahnafalahkalpa/module-pharmacy','provider' => 'Hanafalah\\ModulePharmacy\\ModulePharmacyServiceProvider'],
        'hanafalah/module-event'                => ['repository' => 'hamzahnafalahkalpa/module-event','provider' => 'Hanafalah\\ModuleEvent\\ModuleEventServiceProvider'],
        'hanafalah/module-manufacture'          => ['repository' => 'hamzahnafalahkalpa/module-manufacture','provider' => 'Hanafalah\\ModuleManufacture\\ModuleManufactureServiceProvider'],
        'hanafalah/module-handwriting'          => ['repository' => 'hamzahnafalahkalpa/module-handwriting','provider' => 'Hanafalah\\ModuleHandwriting\\ModuleHandwritingServiceProvider'],
        'hanafalah/module-monitoring'           => ['repository' => 'hamzahnafalahkalpa/module-monitoring','provider' => 'Hanafalah\\ModuleMonitoring\\ModuleMonitoringServiceProvider'],
        'hanafalah/module-support'              => ['repository' => 'hamzahnafalahkalpa/module-support','provider' => 'Hanafalah\\ModuleSupport\\ModuleSupportServiceProvider'],
        'hanafalah/module-tax'                  => ['repository' => 'hamzahnafalahkalpa/module-tax','provider' => 'Hanafalah\\ModuleTax\\ModuleTaxServiceProvider'],
        'hanafalah/satu-sehat'                  => ['repository' => 'hamzahnafalahkalpa/satu-sehat','provider' => 'Hanafalah\\SatuSehat\\SatuSehatServiceProvider'],
        'hanafalah/module-license'              => ['repository' => 'hamzahnafalahkalpa/module-license','provider' => 'Hanafalah\\ModuleLicense\\ModuleLicenseServiceProvider'],
        'wellmed-plus/ms-plus-apotek'           => ['repository' => 'hamzahnafalahkalpa/ms-plus-apotek','provider' => 'WellmedPlus\\MsPlusApotek\\MsPlusApotekServiceProvider'],
        'wellmed-plus/ms-plus-emr'              => ['repository' => 'hamzahnafalahkalpa/ms-plus-emr','provider' => 'WellmedPlus\\MsPlusEmr\\MsPlusEmrServiceProvider'],
        'wellmed-plus/ms-plus-hr'               => ['repository' => 'hamzahnafalahkalpa/ms-plus-hr','provider' => 'WellmedPlus\\MsPlusHr\\MsPlusHrServiceProvider'],
        'wellmed-plus/ms-plus-point-of-sale'    => ['repository' => 'hamzahnafalahkalpa/ms-plus-point-of-sale','provider' => 'WellmedPlus\\MsPlusPointOfSale\\MsPlusPointOfSaleServiceProvider'],
        'wellmed-plus/ms-plus-scm'              => ['repository' => 'hamzahnafalahkalpa/ms-plus-scm','provider' => 'WellmedPlus\\MsPlusScm\\MsPlusScmServiceProvider']
    ]
];
