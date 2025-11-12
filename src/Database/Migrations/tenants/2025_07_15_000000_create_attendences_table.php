<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleEmployee\Models\Attendence\{
    AbsenceRequest,
    Attendence,
    Shift
};
use Hanafalah\ModuleEmployee\Models\Employee\Employee;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Attendence', Attendence::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $employee = app(config('database.models.Employee',Employee::class));
                $shift    = app(config('database.models.Shift',Shift::class));
                $absence_request = app(config('database.models.AbsenceRequest',AbsenceRequest::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($employee::class)
                     ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($shift::class)->nullable()
                     ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($absence_request::class)->nullable()
                     ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->string('status',100)->nullable();
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
