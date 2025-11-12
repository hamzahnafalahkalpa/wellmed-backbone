<?php

use Hanafalah\ModulePayment\Enums\UserWallet\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\Consument\{UserWallet, Wallet};

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.UserWallet', UserWallet::class));
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
                $wallet = app(config('database.models.Wallet', Wallet::class));

                $table->ulid('id')->primary();
                $table->string('user_wallet_code', 50)->nullable(true);
                $table->string('uuid', 36)->nullable(false);
                $table->foreignIdFor($wallet::class)->nullable(false)->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->string('consument_type')->nullable(false);
                $table->ulid('consument_id')->nullable(false);
                $table->decimal('balance', 20, 2)->nullable(false)->default(0);
                $table->datetime('verified_at')->nullable();
                $table->datetime('suspended_at')->nullable();
                $table->string('status', 50)->nullable(false)->default(Status::DRAFT->value);
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
