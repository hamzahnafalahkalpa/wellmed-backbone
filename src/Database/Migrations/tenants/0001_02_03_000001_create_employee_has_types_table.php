<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleEmployee\Models\Employee\Employee;
use Hanafalah\ModuleEmployee\Models\EmployeeType\EmployeeHasType;
use Hanafalah\ModuleEmployee\Models\EmployeeType\EmployeeType;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.EmployeeHasType', EmployeeHasType::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $employee = app(config('database.models.Employee', Employee::class));
                $employee_type = app(config('database.models.EmployeeType',EmployeeType::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($employee::class)->nullable(false)
                      ->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignIdFor($employee_type::class)->nullable(false)
                      ->constrained($employee_type->getTable(), 'id', 'eht_fk')->restrictOnDelete()->cascadeOnUpdate();
                $table->unsignedTinyInteger('current')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTable());
    }
};
