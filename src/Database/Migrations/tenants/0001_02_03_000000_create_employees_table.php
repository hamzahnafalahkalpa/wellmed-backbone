<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleEmployee\Enums\Employee\EmployeeStatus;
use Hanafalah\ModuleEmployee\Models\Attendence\Shift;
use Hanafalah\ModuleEmployee\Models\Employee\Employee;
use Hanafalah\ModuleEmployee\Models\EmployeeType\EmployeeType;
use Hanafalah\ModulePeople\Models\People\People;
use Hanafalah\ModuleProfession\Models\Occupation\Occupation;
use Hanafalah\ModuleProfession\Models\Profession\Profession;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Employee', Employee::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $people        = app(config('database.models.People', People::class));
                $profession    = app(config('database.models.Profession', Profession::class));
                $occupation    = app(config('database.models.Occupation', Occupation::class));
                $employee_type = app(config('database.models.EmployeeType', EmployeeType::class));
                $shift         = app(config('database.models.Shift', Shift::class));

                $table->ulid('id')->primary();
                $table->string('uuid', 36)->nullable();
                $table->string('name',100)->nullable(false);

                $table->foreignIdFor($people::class)
                    ->nullable(false)->index()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($employee_type::class)->nullable(true)
                    ->constrained($employee_type->getTable(), 'id', 'emp_fk')
                    ->restrictOnDelete()->cascadeOnUpdate();

                $table->foreignIdFor($profession::class)
                    ->nullable(true)->index()
                    ->cascadeOnUpdate()->restrictOnDelete();
            
                $table->foreignIdFor($occupation::class)
                    ->nullable(true)->index()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($shift::class)
                    ->nullable(true)->index()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('hired_at',50)->nullable();

                $table->string('profile',255)->nullable();

                $table->enum('status',array_column(EmployeeStatus::cases(),'value'))
                    ->default(EmployeeStatus::DRAFT->value)
                    ->nullable(false);
                $table->json('props')->nullable();
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
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
