<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleMonitoring\Models\{
    ModelHasMonitoring
};

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ModelHasMonitoring', ModelHasMonitoring::class));
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
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $monitoring = app(config('database.models.Monitoring', \Hanafalah\ModuleMonitoring\Models\Monitoring::class));
                
                $table->ulid('id')->primary();
                $table->foreignIdFor($monitoring::class)->nullable(false)
                      ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->string('reference_type',50)->nullable(false);
                $table->string('reference_id',36)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['monitoring_id', 'reference_type', 'reference_id'], 
                               'monitoring_reference_unique');
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
