<?php

namespace Projects\WellmedBackbone\Schemas\ModuleWorkspace;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspaceData;
use Hanafalah\ModuleWorkspace\Schemas\Workspace as SchemasWorkspace;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleWorkspace\Workspace as ContractWorkspace;
use Projects\WellmedBackbone\Jobs\SendOrganizationToSatuSehatJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Workspace extends SchemasWorkspace implements ContractWorkspace
{
    public function prepareStoreWorkspace(WorkspaceData $workspace_dto): Model{
        $model = parent::prepareStoreWorkspace($workspace_dto);
        $this->fillingProps($model,$workspace_dto->props);
        $model->save();
        if (!isset($model->integration)){
            $model->setAttribute('integration',$model->getIntegrationPayload());
        }

        $integration = $model->integration;
        $ihs_number = $integration['satu_sehat']['general']['ihs_number'];
        if (!isset($ihs_number)){
            $this->prepareStoreSatuSehatOrganization($model);
        }
        $model->save();
        return $this->workspace_model = $model;
    }

    public function prepareStoreSatuSehatOrganization(Model $model,?string $connection = 'rabbitmq'){
        $payload = $this->prepareSatuSehatPayload($model);
        $this->dispatchSatuSehatSync($model, $payload, $connection);
    }

    /**
     * Prepare the payload for Satu Sehat integration.
     */
    private function prepareSatuSehatPayload(Model &$model): array
    {
        $existingPayload = $model->organizationSatuSehat?->payload ?? [];
        $basePayload = $this->buildBaseLocationPayload($model);
        $payload = array_merge($existingPayload, $basePayload);
        return $payload;
    }

    /**
     * Build base location payload with essential location information.
     */
    private function buildBaseLocationPayload(Model &$model): array
    {
        return [
            "active" => true,
            // "organization_code" => config('satu-sehat.client_organization_id') ?? config('satu-sehat.organization_id'),
            "organization_code" => null,
            // "organization_code" => config('satu-sehat.client_organization_id') ?? config('satu-sehat.organization_id'),
            "organization_name" => $model->name.' - '.$model->uuid,
            "part_of_organization_code" => config('satu-sehat.organization_id'),
            // "part_of_organization_code" => config('satu-sehat.client_organization_id') ?? config('satu-sehat.organization_id'),
            "type" => [
                "dept" => "PRAKTEK UMUM"
            ],
            "address" => [
                "work" => [
                    "name" => "-",
                    "city" => "Bandung",
                    // "postal_code" => "11290",
                    "province_code" => "31",
                    "city_code" => "3171",
                    "district_code" => "317107",
                    "village_code" => "3171071004",
                    // "rw" => "4",
                    // "rt" => "50"
                ]
            ],
            "telecom" => [
                "work" => [
                    "phone" => [$model->phone ?? "0812######"],
                ]
            ],
        ];
    }

    private function dispatchSatuSehatSync(Model $workspace, array $payload, ?string $connection = 'rabbitmq'): void
    {
        if (!config('module-workspace.satu-sehat.enable', true)) {
            return;
        }

        $tenant_id = tenancy()->tenant->getKey();
        $workspace_id = $workspace->getKey();
        try {
            dispatch(new SendOrganizationToSatuSehatJob(
                $tenant_id,
                $workspace_id,
                $payload
            ))->onQueue('satusehat')->onConnection($connection);

            Log::channel('satu-sehat')->info('Organization queued for Satu Sehat sync', [
                'workspace_id' => $workspace_id,
                'tenant_id' => $tenant_id
            ]);
        } catch (\Throwable $exception) {
            Log::channel('satu-sehat')->error('Failed to queue organization for Satu Sehat', [
                'workspace_id' => $workspace_id,
                'error' => $exception->getMessage()
            ]);
        }
    }
}
