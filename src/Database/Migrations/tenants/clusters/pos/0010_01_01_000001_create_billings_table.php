<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\{
    Models\Transaction\Billing,
};
use Hanafalah\ModulePayment\Enums\Billing\Status;
use Hanafalah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Billing', Billing::class));
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
                $table->string('uuid', 36)->nullable();
                $table->string('billing_code', 100)->nullable();
                $table->foreignIdFor($transaction::class,'has_transaction_id')->nullable()->index();
                $table->string('author_type', 50)->nullable(false);
                $table->string('author_id', 36)->nullable(false);
                $table->string('cashier_type', 50)->nullable(true);
                $table->string('cashier_id', 36)->nullable(true);
                $table->string('status', 50)->default(Status::DRAFT->value)->nullable(false);
                $table->timestamp('reported_at')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['author_type', 'author_id']);
                $table->index(['cashier_type', 'cashier_id']);
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
