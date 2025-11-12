<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleProcurement\Models\{
    PurchaseOrder,
    Purchasing,
    ReceiveOrder
};

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ReceiveOrder', ReceiveOrder::class));
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
                $purchasing = app(config('database.models.Purchasing', Purchasing::class));
                $purchase_order = app(config('database.models.PurchaseOrder', PurchaseOrder::class));

                $table->ulid('id')->primary();
                $table->string('name', 255)->nullable(false);
                $table->date('received_at')->nullable(true);
                $table->foreignIdFor($purchasing::class)->nullable(false)
                      ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($purchase_order::class)->nullable(false)
                      ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
