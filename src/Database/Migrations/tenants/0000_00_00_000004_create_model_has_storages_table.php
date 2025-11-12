<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleWarehouse\Models\Storage\ModelHasStorage;
use Hanafalah\ModuleWarehouse\Models\Storage\Storage;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ModelHasStorage;', ModelHasStorage::class));
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
                $storage = app(config('database.models.Storage', Storage::class));

                $table->ulid('id')->primary();
                $table->string('model_type', 50)->nullable(false);
                $table->string('model_id', 36)->nullable(false);

                $table->foreignIdFor($storage::class)->nullable(false)
                    ->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('name', 50)->nullable(false);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['model_type', 'model_id', $storage->getForeignKey()], 'model_strg_mhs');
                $table->index(['model_type', 'model_id'], 'model_mhs');
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
