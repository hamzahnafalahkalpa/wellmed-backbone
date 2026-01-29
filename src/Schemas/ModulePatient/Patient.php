<?php

namespace Projects\WellmedBackbone\Schemas\ModulePatient;

use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Schemas\Patient as SchemasPatient;
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\Patient as ModulePatientPatient;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Patient extends SchemasPatient implements ModulePatientPatient
{
    protected function afterPatientCreated(Model &$patient, PatientData &$patient_dto): self{
        parent::afterPatientCreated($patient, $patient_dto);
        $patient_dto->props['integration'] = $patient->integration ?? $this->requestDTO(
            config('app.contracts.IntegrationData'),[
            ]
        );
        if (!is_array($patient_dto->props['integration'])){
            $integration = $patient_dto->props['integration']->toArray();
        }else{
            $integration = $patient_dto->props['integration'];
        }
        $patient->setAttribute('integration',$integration);
        $patient->load([
            'patientSatuSehat',
            'reference.addresses' => function($query){
                $query->with([
                    'province','district','subdistrict','village'
                ]);
            }
        ]);
        $patient_satu_sehat_log = $patient->patientSatuSehat;
        $form_payload = array_merge($patient_satu_sehat_log?->payload ?? [
            'name'       => $patient->name,
            'gender'     => Str::lower($patient->prop_people['sex']),
            'nik'        => $patient->prop_people['card_identity']['nik'] ?? null,
            'passport'   => $patient->prop_people['card_identity']['passport'] ?? null,
            'birth_date' => $patient->prop_people['dob'] ?? null
        ]);
        if (isset($form_payload['nik_ibu'])){
            unset($form_payload['nik']);
            $form_payload['nik_ibu'] = $patient->prop_people['card_identity']['nik_ibu'];
        }
        $addresses = $form_payload['address'] ?? [];
        foreach ($patient->reference->addresses ?? [] as $address) {
            $new_address = [];
            switch ($address->flag){
                case 'KTP'       : $type = 'home';break;
                case 'RESIDENCE' : $type = 'temp';break;
            }
            if (isset($addresses[$type])){
                continue;
            }else{
                if (isset($address->province) && isset($address->district) 
                    && isset($address->subdistrict) && isset($address->village)){
                    if (isset($new_address[$type])){
                        continue;
                    }
                    if (isset($address->province)) $province_code = Str::replace('.','',$address->province->code);
                    if (isset($address->district)) {
                        // $district_code = Str::replace('.','',Str::afterLast($address->district->code,'.'));
                        $district_code = Str::replace('.','',$address->district->code);
                        $district_name = $address->district->name;
                    }
                    if (isset($address->subdistrict)) $subdistrict_code = Str::replace('.','',$address->subdistrict->code);
                    if (isset($address->village)) $village_code = Str::replace('.','',$address->village->code);
                    $new_address[$type] = [
                        'name'          => $address->name,
                        'city'          => $district_name ?? null,
                        'postal_code'   => $address->zip_code,
                        'province_code' => $province_code ?? null,
                        'city_code'     => $district_code ?? null,
                        'district_code' => $subdistrict_code ?? null,
                        'village_code'  => $village_code ?? null,
                        'rw'            => $address->rw,
                        'rt'            => $address->rt,
                    ];
                }
            }
        }
        $form_payload['address'] = array_merge($addresses,$new_address);
        if (config('module-patient.satu-sehat.enable', true)){
            try {
                $patient_satu_sehat = $this->schemaContract('patient_satu_sehat')->useAccessToSatuSehat()
                    ->prepareStorePatientSatuSehat(
                    $this->requestDTO(
                        config('app.contracts.PatientSatuSehatData'),[
                            'model' => $patient,
                            'form'  => $form_payload
                        ]
                    )
                );
                $prop_card_identity = $patient->prop_card_identity ?? [];
                $prop_card_identity['ihs_number'] = $patient_satu_sehat->response['id'] ?? null;
                $patient->setAttribute('prop_card_identity',$prop_card_identity);
                $patient->save();                
            } catch (\Throwable $th) {
                Log::channel('satu-sehat')->error($th->getMessage());
            }
        }

        $this->fillingProps($patient, $patient_dto->props);
        $patient->save();
        return $this;
    }
}