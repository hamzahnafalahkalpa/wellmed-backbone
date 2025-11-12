<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleWarehouse\Models\Stock\{
    Batch,
    StockBatch,
    Stock
};

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.StockBatch', StockBatch::class));
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
                $stock = app(config('database.models.Stock', Stock::class));
                $batch = app(config('database.models.Batch', Batch::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($stock::class)->nullable(false)
                    ->index()->constrained()
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreignIdFor($batch::class)->nullable(false)
                    ->index()->constrained()
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->decimal('stock', 12, 4)->nullable(false);
                $table->json('props')->nullable(true);
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after($this->__table->getKeyName())
                    ->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();
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
