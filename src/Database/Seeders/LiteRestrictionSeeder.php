<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class LiteRestrictionSeeder extends Seeder
{
    use HasRequestData;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $medic_services = app(config('database.models.MedicService'))->withoutGlobalScopes(['restriction'])->get();
        $skips = [
            'ADMINISTRASI', 'RAWAT JALAN'
        ];
        $restriction_schema = app(config('app.contracts.RestrictionFeature'));

        foreach ($medic_services as $medic_service) {
            if (in_array($medic_service->label,$skips)) {
                $medic_service->is_restricted = false;
                $medic_service->save();
            }else{
                $restriction_schema->prepareStoreRestrictionFeature($this->requestDTO(config('app.contracts.RestrictionFeatureData'),[
                    'model_type' => $medic_service->getMorphClass(),
                    'model_id' => $medic_service->getKey(),
                    'reference_type' => 'Tenant',
                    'reference_id' => tenancy()->tenant->getKey()
                ]));
            }
        }

        $permissions = app(config('database.models.Permission'))->withoutGlobalScopes(['restriction'])->whereIn('type',['MENU','MODULE'])->get();
        $skips = [
            'api.setting.index',
            'api.setting.index.faskes-service.index',
            'api.setting.index.faskes-service.patient-type.index',
            'api.setting.index.faskes-service.patient-type-service.index',
            'api.setting.index.finance.index',
            'api.setting.index.finance.tariff-component.index',
            'api.setting.index.finance.payment-method.index',
            'api.setting.index.general-setting.index',
            'api.setting.index.general-setting.encoding.index',
            'api.setting.index.general-setting.workspace.index',
            'api.setting.index.item-management.index',
            'api.setting.index.item-management.selling-form.index',
            'api.setting.index.treatment.index',
            'api.setting.index.treatment.medical-treatment.index',
            'api.setting.index.satu-sehat-integration.index',
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
                    'model_id' => $permission->getKey(),
                    'reference_type' => 'Tenant',
                    'reference_id' => tenancy()->tenant->getKey()
                ]));
            }
        }
    }
}
