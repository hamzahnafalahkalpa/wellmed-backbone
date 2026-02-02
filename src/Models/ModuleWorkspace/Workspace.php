<?php

namespace Projects\WellmedBackbone\Models\ModuleWorkspace;

use Hanafalah\ModuleLicense\Concerns\HasLicense;
use Hanafalah\ModuleLicense\Concerns\HasModelHasLicense;
use Hanafalah\ModuleWorkspace\Models\Workspace\Workspace as WorkspaceWorkspace;
use Projects\WellmedBackbone\Transformers\Workspace\SettingWorkspace;

class Workspace extends WorkspaceWorkspace
{
    use HasLicense, HasModelHasLicense;

    protected $integration = [
        'integration' => [
            "satu_sehat" => [
                "progress" => 0,
                "general" => [
                    "ihs_number" => null
                ],
                "syncs" => [
                    [
                        'flag' => 'encounter',
                        'label' => 'Kunjungan',
                    ],
                    [
                        'flag' => 'condition',
                        'label' => 'Diagnosa',
                    ], 
                    [
                        'flag' => 'dispense',
                        'label' => 'Resep',
                    ]
                ]
            ],
            "bpjs" => [
                "progress" => 0,
                "syncs" => [
                    [
                        'flag' => 'encounter',
                        'label' => 'Kunjungan',
                    ],
                    [
                        'flag' => 'condition',
                        'label' => 'Diagnosa',
                    ], 
                    [
                        'flag' => 'dispense',
                        'label' => 'Resep',
                    ]
                ]
            ]
        ]
    ];

    public function getIntegrationPayload(){
        return $this->integration;
    }

    public function getSettingResource(){
        return SettingWorkspace::class;
    }

    public function installedFeatures(){
        return $this->morphManyModel('InstalledFeature','model');
    }

    public function organizationSatuSehat(){return $this->morphOneModel('OrganizationSatuSehat','reference');}
}
