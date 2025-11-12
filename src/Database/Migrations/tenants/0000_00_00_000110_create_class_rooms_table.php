<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleClassRoom\Models\ClassRoom\ClassRoom;
use Hanafalah\ModuleService\Models\Service;

return new class extends Migration
{
    use NowYouSeeMe;
    
    private $__table, $__table_medic_service;

    public function __construct()
    {
        $this->__table = app(config('database.models.ClassRoom', ClassRoom::class));
        $this->__table_medic_service = app(config('database.models.Service', Service::class));
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
                $table->string('name')->nullable(false);
                $table->string('status',50)->default('ACTIVE');
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table_medic_service,'service_type_id')->nullable()
                      ->after('id')->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
