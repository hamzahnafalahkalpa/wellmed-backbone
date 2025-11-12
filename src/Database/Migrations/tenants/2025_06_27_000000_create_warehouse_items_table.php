<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleWarehouse\Models\{
    WarehouseItem
};

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.WarehouseItem', WarehouseItem::class));
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
                $table->string('warehouse_type',50)->nullable(false);
                $table->string('warehouse_id',36)->nullable(false);
                $table->string('item_type',50)->nullable(false);
                $table->string('item_id',36)->nullable(false);
                $table->string('flag',50)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['warehouse_type', 'warehouse_id'], 'wh_wh_item');
                $table->index(['item_type', 'item_id'], 'wh_item_item');
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
