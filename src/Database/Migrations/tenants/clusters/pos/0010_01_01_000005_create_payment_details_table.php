<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\{
    Models\Payment\PaymentDetail
};
use Hanafalah\ModulePayment\Models\Payment\PaymentHistory;
use Hanafalah\ModulePayment\Models\Payment\PaymentSummary;
use Hanafalah\ModulePayment\Models\Transaction\Invoice;
use Hanafalah\ModulePayment\Models\Transaction\PosTransaction;
use Hanafalah\ModulePayment\Models\Transaction\PosTransactionItem;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PaymentDetail', PaymentDetail::class));
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
                $invoice                = app(config('database.models.Invoice', Invoice::class));
                $payment_summary        = app(config('database.models.PaymentSummary', PaymentSummary::class));
                $transaction_item       = app(config('database.models.PosTransactionItem', PosTransactionItem::class));
                $payment_history        = app(config('database.models.PaymentHistory', PaymentHistory::class));
                $transaction            = app(config('database.models.PosTransaction', PosTransaction::class));

                $table->ulid('id')->primary();
                $table->string('name',255)->nullable();
                $table->foreignIdFor($payment_summary::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($invoice::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($payment_history::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($transaction_item::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();   
                    
                $table->foreignIdFor($transaction::class)->nullable()->index();   

                $table->unsignedInteger('amount')->nullable()->default(0);
                $table->unsignedInteger('qty')->nullable()->default(0);
                $table->unsignedInteger('cogs')->nullable()->default(0);
                $table->unsignedInteger('debt')->nullable()->default(0);
                $table->unsignedInteger('price')->nullable()->default(0);
                $table->unsignedInteger('paid')->nullable()->default(0);
                $table->unsignedInteger('refund')->nullable()->default(0);
                $table->unsignedInteger('discount')->nullable()->default(0);
                $table->unsignedTinyInteger('tax')->nullable()->default(0);
                $table->unsignedInteger('additional')->nullable()->default(0);
                $table->boolean('is_loan')->nullable()->default(0);

                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
