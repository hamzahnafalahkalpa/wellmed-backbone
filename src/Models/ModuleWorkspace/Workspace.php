<?php

namespace Projects\WellmedBackbone\Models\ModuleWorkspace;

use Hanafalah\ModuleLicense\Concerns\HasLicense;
use Hanafalah\ModuleLicense\Concerns\HasModelHasLicense;
use Hanafalah\ModuleWorkspace\Models\Workspace\Workspace as WorkspaceWorkspace;
use Projects\WellmedBackbone\Transformers\Workspace\SettingWorkspace;
use Hanafalah\ModuleRegional\Data\AddressData;

class Workspace extends WorkspaceWorkspace
{
    use HasLicense, HasModelHasLicense;

    protected $integration = [
        'integration' => [
            "satu_sehat" => [
                'flag' => 'satu-sehat',
                'progress' => 0,
                'last_updated_at' => null,
                'from' => 0,
                'to' => 0,
                "general" => [
                    "ihs_number" => null
                ],
                "syncs" => [
                    [
                        'flag' => 'patient',
                        'label' => 'Pasien',
                        "from" => 0,
                        "to" => 0,
                        "progress" => 0,
                        "last_updated_at" => null
                    ],
                    [
                        'flag' => 'encounter',
                        'label' => 'Kunjungan',
                        "from" => 0,
                        "to" => 0,
                        "progress" => 0,
                        "last_updated_at" => null
                    ],
                    [
                        'flag' => 'observation',
                        'label' => 'Observasi',
                        "from" => 0,
                        "to" => 0,
                        "progress" => 0,
                        "last_updated_at" => null
                    ]
                ],
                'logs' => []
            ],
            "bpjs" => [
                'flag' => 'bpjs',
                'progress' => 0,
                'last_updated_at' => null,
                'from' => 0,
                'to' => 0,
                "syncs" => [
                    [
                        'flag' => 'patient',
                        'label' => 'Pasien',
                        "from" => 0,
                        "to" => 0,
                        "progress" => 0,
                        "last_updated_at" => null
                    ],
                    [
                        'flag' => 'encounter',
                        'label' => 'Kunjungan',
                        "from" => 0,
                        "to" => 0,
                        "progress" => 0,
                        "last_updated_at" => null
                    ],
                    [
                        'flag' => 'observation',
                        'label' => 'Observasi',
                        "from" => 0,
                        "to" => 0,
                        "progress" => 0,
                        "last_updated_at" => null
                    ]
                ],
                'logs' => []
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

    public function setAddress($flag, array|object $address){
        if (isset($flag)) {
            if (is_object($address) && !$address instanceof AddressData) {
            throw new \Exception('address must be an instance of AddressData');
            }elseif (is_array($address)){
            unset($address['village_model']);
            unset($address['subdistrict_model']);
            $address = AddressData::from($this->mergeArray($address,[
                'flag'       => $flag, 
                'model_id'   => $this->getKey(),
                'model_type' => $this->getMorphClass()
            ]));
            }

            $address = app(config('app.contracts.WellmedAddress'))
                    ->prepareStoreAddress(
                        app(config('app.contracts.AddressData'))::from($address)
                        // AddressData::from($address)
                    );
            return $address;
        } else {
            return null;
        }
    }

    public function address(){return $this->morphOneModel('WellmedAddress','model');}
    public function addresses(){return $this->morphManyModel('WellmedAddress','model');}
}
