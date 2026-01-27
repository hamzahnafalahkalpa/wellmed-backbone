<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleAppointment\Models\{
    Kiosk,
    QueueTransaction
};
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;

    public function __construct()
    {
        $this->__table = app(config('database.models.QueueTransaction', QueueTransaction::class));
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
                $kiosk = app(config('database.models.Kiosk',Kiosk::class));
                
                $table->ulid('id')->primary();
                $table->foreignIdFor($kiosk::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('queue_number', 255)->nullable(false);
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
