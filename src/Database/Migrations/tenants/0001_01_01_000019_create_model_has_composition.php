<?php

use Hanafalah\ModuleItem\Models\{
    ModelHasComposition,
    Composition
};

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ModelHasComposition', ModelHasComposition::class));
    }

    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $composition = app(config('database.models.Composition', Composition::class));

                $table->ulid('id')->primary();
                $table->string('model_type', 50)->nullable(false);
                $table->string('model_id', 36)->nullable(false);
                $table->foreignIdFor($composition::class)->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->json("props")->nullable();

                $table->index(['model_type', 'model_id'], 'model_cp_ref');
                $table->index(['model_type', 'model_id', 'composition_id'], 'model_full_ref');
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
