<?php

use Hanafalah\ModulePayment\Models\Transaction\Billing;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\Transaction\Invoice;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Invoice', Invoice::class));
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
                $billing = app(config('database.models.Billing', Billing::class));

                $table->ulid('id')->primary();
                $table->string('invoice_code')->nullable();
                $table->string('flag',100)->nullable();
                $table->string('author_id')->nullable();
                $table->string('author_type')->nullable();
                $table->string('payer_id')->nullable();
                $table->string('payer_type')->nullable();
                $table->foreignIdFor($billing::class)->nullable()->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->timestamp('reported_at')->nullable();
                $table->timestamp('paid_at')->nullable()->comment('Paid at');
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['author_id', 'author_type'], 'author_ref');
                $table->index(['payer_id', 'payer_type'], 'payer_inv');
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
