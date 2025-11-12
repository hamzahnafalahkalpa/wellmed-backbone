<?php

use Hanafalah\ModulePayment\Models\Consument\UserWallet;
use Hanafalah\ModulePayment\Models\Payment\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\Transaction\Invoice;
use Hanafalah\ModulePayment\Models\Transaction\SplitPayment;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.SplitPayment', SplitPayment::class));
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
                $invoice = app(config('database.models.Invoice', Invoice::class));
                $payment_method = app(config('database.models.PaymentMethod', PaymentMethod::class));
                $user_wallet = app(config('database.models.UserWallet', UserWallet::class));

                $table->ulid('id')->primary();
                $table->string('payment_method', 36)->nullable(true);
                $table->foreignIdFor($invoice::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignIdFor($payment_method::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignIdFor($user_wallet::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->unsignedBigInteger('money_paid')->nullable(true)->default(0);
                $table->unsignedBigInteger('total_paid')->nullable(true)->default(0);
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
