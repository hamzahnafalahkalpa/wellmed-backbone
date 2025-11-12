<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\Transaction\TransactionHasConsument;
use Hanafalah\ModulePayment\Models\Consument\Consument;
use Hanafalah\ModulePayment\Models\Transaction\PosTransaction;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.TransactionHasConsument', TransactionHasConsument::class));
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
                $consument   = app(config('database.models.Consument', Consument::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($transaction::class,'transaction_id')->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($consument::class)->nullable()->index();
                $table->json('props')->nullable();
                $table->timestamps();
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
