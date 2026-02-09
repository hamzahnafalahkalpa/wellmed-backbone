<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;
use Hanafalah\LaravelSupport\Jobs\JobRequest;
use Illuminate\Support\Facades\Log;

class LiteRestrictionSeeder extends Seeder
{
    use HasRequestData;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $workspace = app(config('database.models.Workspace'))->with('installedFeatures')->findOrFail($data['workspace_id']);
        
        //ADD LICENSE : 1 SUDAH DI USER ADMIN SEEDER, SISA 2 UNTUK LITE
        for ($i=0; $i < 1; $i++) { 
            $now = now();
            app(config('app.contracts.License'))->prepareStoreLicense($this->requestDTO(
                config('app.contracts.LicenseData'),[
                    'reference_type'    => 'Workspace',
                    'reference_id'      => (string) $data['workspace_id'],
                    'name' => null,
                    'expired_at'        => $now->addMonth(),
                    'last_paid'         => $now,
                    'billing_generated_at' => $now,
                    'is_billing_generated' => false,
                    'status'            => 'ACTIVE',
                    'recurring_type'    => 'MONTHLY',
                    'flag'              => 'USER_LICENSE'
                ]
            ));
        }

        $medic_services = app(config('database.models.MedicService'))->withoutGlobalScopes(['restriction'])->get();
        $skips = [
            'ADMINISTRASI', 'RAWAT JALAN'
        ];
        $room_payloads = [];
        $building = app(config('database.models.Building'))->first();
        foreach ($workspace->installedFeatures as $installed_feature) {
            if ($installed_feature->master_feature_type == 'MedicService'){
                $wellmed_medic_service = app(config('database.models.WellmedUnicode'))->withoutGlobalScope('flag')->findOrFail($installed_feature->master_feature_id);
                $medic_service = app(config('database.models.MedicService'))->withoutGlobalScopes()->where('flag','MedicService')->where('label',$wellmed_medic_service->label)->first();
                $skips[] = $medic_service->label;
                $room_payloads[] = [
                    "name" => "Ruang ".$medic_service->name,
                    "floor"=> 1,
                    "phone"=> null,
                    "medic_service_id"=> $medic_service->getKey(), //nullable, GET FROM SETTING > FASKES SERVICE > MEDICAL SERVICE
                    'medic_service_model' => $medic_service,
                    "building_id"=> $building->getKey(),
                ];
            }
        }

        $restriction_schema = app(config('app.contracts.RestrictionFeature'));

        foreach ($medic_services as $medic_service) {
            if (in_array($medic_service->label,$skips)) {
                $medic_service->is_restricted = false;
            }else{
                $medic_service->is_restricted = true;
                $restriction_schema->prepareStoreRestrictionFeature($this->requestDTO(config('app.contracts.RestrictionFeatureData'),[
                    'model_type' => $medic_service->getMorphClass(),
                    'model_id' => $medic_service->getKey(),
                    'reference_type' => 'Tenant',
                    'reference_id' => (string) tenancy()->tenant->getKey()
                ]));
            }
            $medic_service->save();
        }
        foreach ($room_payloads as $room_payload) {
            $room_payload['ihs_number'] = $workspace->integration['satu_sehat']['general']['ihs_number'] ?? null;
            $room_model = app(config('app.contracts.Room'))->prepareStoreRoom(
                $this->requestDTO(config('app.contracts.RoomData'),$room_payload)
            );
        }
        $permissions = app(config('database.models.Permission'))->withoutGlobalScopes(['restriction'])->whereIn('type',['MENU','MODULE'])->get();
        $skips = [
            'api.setting.index',
            'api.setting.faskes-service.index',
            'api.setting.faskes-service.patient-type.index',
            'api.setting.faskes-service.patient-type-service.index',
            'api.setting.finance.index',
            'api.setting.finance.tariff-component.index',
            'api.setting.finance.payment-method.index',
            'api.setting.general-setting.index',
            'api.setting.general-setting.encoding.index',
            'api.setting.general-setting.workspace.index',
            'api.setting.item-management.index',
            'api.setting.item-management.selling-form.index',
            'api.setting.treatment.index',
            'api.setting.treatment.medical-treatment.index',
            'api.setting.satu-sehat-integration.index',
            'api.setting.satu-sehat-integration.general-setting.index',
            'api.pharmacy-department.index',
            'api.pharmacy-department.frontline.*',
            'api.transaction.index',
            'api.transaction.point-of-sale.index',
            'api.transaction.point-of-sale.show.billing.index',
            'api.transaction.point-of-sale.show.billing.show.invoice.index',
            'api.transaction.billing.index',
            'api.transaction.billing.show.invoice.index',
            'api.transaction.billing.show.invoice.show.refund.index',
            'api.transaction.invoice.index',
            'api.transaction.invoice.show.refund.index',
            'api.employee-management.index',
            'api.employee-management.employee.index',
            'api.item-management.index',
            'api.item-management.item.index',
            'api.reporting.index',
        ];
        $patient_emr_aliases = app(config('database.models.Permission'))->whereLike('alias','api.patient-emr%')->whereIn('type',['MENU','MODULE'])->get();
        $skips = array_merge($skips,$patient_emr_aliases->pluck('alias')->toArray());
        foreach ($permissions as $permission) {
            if (in_array($permission->alias,$skips)) {
                $permission->is_restricted = false;
                $permission->save();
            }else{
                $restriction_schema->prepareStoreRestrictionFeature($this->requestDTO(config('app.contracts.RestrictionFeatureData'),[
                    'model_type' => $permission->getMorphClass(),
                    'model_id' => (string) $permission->getKey(),
                    'reference_type' => 'Tenant',
                    'reference_id' => (string) tenancy()->tenant->getKey()
                ]));
            }
        }
    }
}
