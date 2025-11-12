<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleEmployee\Models\Attendence\EmployeeShift;
use Hanafalah\ModuleEmployee\Models\Attendence\Shift;
use Hanafalah\ModuleEmployee\Models\Employee\Employee;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.EmployeeShift', EmployeeShift::class));
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
                $shift    = app(config('database.models.Shift', Shift::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($employee::class)
                      ->index()->constrained()->cascadeOnDelete();
                $table->foreignIdFor($shift::class)
                      ->index()->constrained()->cascadeOnDelete();
                $table->date('shift_date');
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
        Schema::dropIfExists($this->__table->getTable());
    }
};
