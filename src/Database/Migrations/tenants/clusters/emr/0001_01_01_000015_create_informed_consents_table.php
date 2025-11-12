<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleInformedConsent\Models\InformedConsent;
use Hanafalah\ModuleInformedConsent\Models\MasterInformedConsent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.InformedConsent', InformedConsent::class));
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
                $master_informed = app(config('database.models.MasterInformedConsent', MasterInformedConsent::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($transaction::class)
                    ->nullable()->index();

                $table->foreignIdFor($master_informed::class)
                    ->nullable()->index()->constrained($master_informed->getTable(), $master_informed->getKey(), 'mi_ms')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string("author_type", 50);
                $table->string("author_id", 36);

                $table->tinyInteger("status");
                $table->json("props");
                $table->timestamps();
                $table->softDeletes();

                $table->index(['author_type', 'author_id'], 'ic_author');
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
