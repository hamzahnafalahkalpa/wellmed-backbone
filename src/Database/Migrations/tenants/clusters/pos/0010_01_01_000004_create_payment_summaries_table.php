<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\{
    Models\Payment\PaymentSummary,
};
use Hanafalah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PaymentSummary', PaymentSummary::class));
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

                $table->ulid('id')->primary();
                $table->foreignIdFor($transaction::class)->nullable()->index();
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->unsignedBigInteger('amount')->nullable()->default(0);
                $table->unsignedBigInteger('debt')->nullable()->default(0);
                $table->unsignedBigInteger('cogs')->nullable()->default(0);
                $table->unsignedBigInteger('discount')->nullable()->default(0);
                $table->unsignedTinyInteger('tax')->nullable()->default(0);
                $table->unsignedBigInteger('additional')->nullable()->default(0);
                $table->unsignedBigInteger('paid')->nullable()->default(0);
                $table->unsignedBigInteger('refund')->nullable()->default(0);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id'], 'ref_payment');
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
