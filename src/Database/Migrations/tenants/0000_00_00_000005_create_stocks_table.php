<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleFunding\Models\Funding\Funding;
use Hanafalah\ModuleWarehouse\Models\Stock\Stock;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Stock', Stock::class));
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
                $funding = app(config('database.models.Funding', Funding::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($funding::class)->nullable(true)
                    ->index()->constrained()
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
                $table->decimal('stock', 12, 4)->nullable(false);
                
                $table->string('subject_type', 50)->nullable(false);
                $table->string('subject_id', 36)->nullable(false);
                $table->string('warehouse_type', 50)->nullable(false);
                $table->string('warehouse_id', 36)->nullable(false);
                $table->string('supplier_type', 50)->nullable(true);
                $table->string('supplier_id', 36)->nullable(true);
                $table->string('procurement_type', 50)->nullable(true);
                $table->string('procurement_id', 36)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['funding_id', 'subject_type', 'subject_id'], 'subject_fund_item');
                $table->index(['subject_type', 'subject_id'], 'subject_item');
                $table->index(['warehouse_type', 'warehouse_id'], 'warehouse_source');
                $table->index([
                    'subject_type',
                    'subject_id',
                    'warehouse_type',
                    'warehouse_id'
                ], 'warehouse_item_src');
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained($table_name)
                    ->cascadeOnUpdate()->restrictOnDelete();
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
