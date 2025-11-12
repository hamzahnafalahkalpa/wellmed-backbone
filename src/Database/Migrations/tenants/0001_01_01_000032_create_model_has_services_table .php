<?php

use Hanafalah\ModuleService\Models\ModelHasService;
use Hanafalah\ModuleService\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ModelHasService', ModelHasService::class));
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
                $service = app(config('database.models.Service', Service::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($service::class)
                    ->nullable()->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
                $table->string("model_id", 36);
                $table->string('model_type', 50);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['model_type', 'model_id'], 'sv_ref');
                $table->index(['model_type', 'model_id', $service->getForeignKey()], 'sv_ref_fk');
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
