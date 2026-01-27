<?php

use Hanafalah\ModuleAppointment\Models\Appointment;
use Hanafalah\ModuleAppointment\Models\QueueTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;

    public function __construct()
    {
        $this->__table = app(config('database.models.Appointment', Appointment::class));
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
                $table->ulid('id')->primary();
                $table->string('name', 255)->nullable();
                $table->string('status')->nullable();
                $table->string('scheduled_at')->nullable();
                $table->timestamp('checked_in_at')->nullable();
                $table->string('reference_type', 50)->nullable();
                $table->string('reference_id', 36)->nullable();
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
