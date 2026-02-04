<?php

namespace Projects\WellmedBackbone\Schemas\ModuleEmployee;

use Hanafalah\ModuleEmployee\Schemas\Employee as SchemasEmployee;
use Illuminate\Database\Eloquent\{
    Builder, Model
};
use Hanafalah\ModuleEmployee\{
    Contracts\Data\EmployeeData,
};
use Illuminate\Support\Str;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleEmployee\Employee as ModuleEmployeeEmployee;
use Projects\WellmedBackbone\Jobs\SyncEmployeeToSatuSehatJob;
use Illuminate\Support\Facades\Log;

class Employee extends SchemasEmployee implements ModuleEmployeeEmployee{
    protected function afterEmployeeCreated(Model &$employee, EmployeeData &$employee_dto): self{
        parent::afterEmployeeCreated($employee, $employee_dto);
        $this->dispatchSatuSehatSync($employee);

        $this->fillingProps($employee, $employee_dto->props);
        $employee->save();
        return $this;
    }

    public function prepareStoreSatuSehatEmployee(Model $employee_model,?string $connection = 'rabbitmq'){
        $this->dispatchSatuSehatSync($employee_model,$connection);
    }

    private function dispatchSatuSehatSync(Model $employee,?string $connection = 'rabbitmq'): void
    {
        $tenant_id = tenancy()->tenant->getKey();
        $employee_id = $employee->getKey();
        try {
            dispatch(new SyncEmployeeToSatuSehatJob(
                $tenant_id,
                $employee_id
            ))->onQueue('satusehat')->onConnection($connection);

            Log::channel('satu-sehat')->info('Employee queued for Satu Sehat sync', [
                'employee_id' => $employee_id,
                'tenant_id' => $tenant_id
            ]);
        } catch (\Throwable $exception) {
            Log::channel('satu-sehat')->error('Failed to queue employee for Satu Sehat', [
                'employee_id' => $employee_id,
                'error' => $exception->getMessage()
            ]);
        }
    }
}

