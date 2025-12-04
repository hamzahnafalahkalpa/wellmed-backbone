<?php

use Hanafalah\ModulePayment\Models\Transaction\PosTransaction;
use Hanafalah\ModulePayment\Models\Transaction\PosTransactionItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PosTransactionItem', PosTransactionItem::class));
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
                $transaction = app(config('database.models.PosTransaction', PosTransaction::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($transaction::class,'transaction_id')->nullable()->index();
                $table->string('name', 255)->nullable(false);
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->string('item_type', 50)->nullable(false);
                $table->string('item_id', 36)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['item_type', 'item_id'], 'trxi_item_ref');
                $table->index(['reference_type', 'reference_id'], 'trxi_ref');
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained()
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
