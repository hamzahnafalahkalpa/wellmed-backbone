<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\Consument\UserWallet;
use Hanafalah\ModulePayment\Models\Consument\Wallet;
use Hanafalah\ModulePayment\Models\Transaction\WalletTransaction;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.WalletTransaction', WalletTransaction::class));
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
                $wallet = app(config('database.models.Wallet',Wallet::class));
                $user_wallet = app(config('database.models.UserWallet',UserWallet::class));

                $table->ulid('id')->primary();
                $table->string('uuid',36)->nullable(false);
                $table->string('name', 255)->nullable(false);
                $table->string('transaction_type', 200)->nullable(false);
                $table->foreignIdFor($wallet)->nullable(false)->index()->constrained()
                        ->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($user_wallet)->nullable(false)->index()->constrained()
                        ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('reference_type')->nullable(false);
                $table->ulid('reference_id')->nullable(false);
                $table->string('consument_type')->nullable(false);
                $table->ulid('consument_id')->nullable(false);
                $table->decimal('previous_balance', 20, 2)->nullable(false);
                $table->decimal('current_balance', 20, 2)->nullable(false);
                $table->decimal('nominal', 20, 2)->nullable(false);
                $table->string('author_type')->nullable(true);
                $table->ulid('author_id')->nullable(true);
                $table->timestamp('reported_at')->nullable(true);
                $table->string('status', 100)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id'], 'wallet_trx_ref');
                $table->index(['consument_type', 'consument_id'], 'wallet_trx_cons');
                
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
