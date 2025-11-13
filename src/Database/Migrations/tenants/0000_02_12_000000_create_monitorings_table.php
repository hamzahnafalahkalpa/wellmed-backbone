<?php

use Hanafalah\ModuleMonitoring\Models\{
    Monitoring, MonitoringCategory
};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Monitoring', Monitoring::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $monitoring_category = app(config('database.models.MonitoringCategory', MonitoringCategory::class));

                $table->ulid('id')->primary();
                $table->string('name');
                $table->foreignIdFor($monitoring_category::class)->nullable()
                      ->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('reference_type', 50);
                $table->string('reference_id', 36);
                $table->string('author_type', 50)->nullable();
                $table->string('author_id', 36)->nullable();
                $table->timestamp('start_date');
                $table->timestamp('end_date')->nullable();
                $table->string('status', 50)->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['name', 'reference_type', 'reference_id'], 'monitoring_unique');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitorings');
    }
};
