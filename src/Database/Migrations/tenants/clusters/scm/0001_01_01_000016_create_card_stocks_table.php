<?php

use Hanafalah\ModuleItem\Models\CardStock;
use Hanafalah\ModuleItem\Models\Item;
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
        $this->__table = app(config('database.models.CardStock', CardStock::class));
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
                $transaction = app(config('database.models.Transaction', Transaction::class));
                $item        = app(config('database.models.Item', Item::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($item::class)->nullable(false)
                    ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

                $table->foreignIdFor($transaction::class)->nullable(false)
                    ->index();

                $table->string('reference_type',50)->nullable();
                $table->string('reference_id',36)->nullable();
                $table->decimal('receive_qty',14,6)->default(0.00)->nullable(false);
                $table->decimal('request_qty',14,6)->default(0.00)->nullable(false);
                $table->decimal('total_qty', 14, 6)->default(0.00)->nullable(false);
                $table->unsignedBigInteger('total_tax')->default(0)->nullable(false);
                $table->unsignedBigInteger('total_cogs')->default(0)->nullable(false);
                
                $table->timestamp('reported_at')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['item_id'], 'cs_item_ref');
                $table->index(['item_id', 'transaction_id'], 'cs_trx_item');
                $table->index(['reference_type','reference_id'], 'cs_ref_item');
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after($this->__table->getKeyName())
                    ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
