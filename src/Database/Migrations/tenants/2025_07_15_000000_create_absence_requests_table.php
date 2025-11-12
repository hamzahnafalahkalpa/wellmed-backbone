<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleEmployee\Models\Attendence\{
    AbsenceRequest
};
use Hanafalah\ModuleEmployee\Models\Employee\Employee;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.AbsenceRequest', AbsenceRequest::class));
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
                
                $table->ulid('id')->primary();
                $table->foreignIdFor($employee::class)->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
                    
                $table->string('absence_type', 255)->nullable(false);
                $table->unsignedInteger('total_day')->nullable();
                $table->string('reason', 255)->nullable();
                $table->string('status', 255)->nullable(false);
                $table->string('approver_type', 255)->nullable(true);
                $table->foreignUlid('approver_id')->nullable(true)->nullOnDelete();
                $table->datetime('approved_at')->nullable(true);
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
