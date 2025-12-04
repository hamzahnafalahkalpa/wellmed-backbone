<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\Accounting\JournalEntry;
use Hanafalah\ModulePayment\Models\Accounting\JournalSource;
use Hanafalah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.JournalEntry', JournalEntry::class));
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
                $transaction = app(config('database.models.Transaction',Transaction::class));
                $journal_source = app(config('database.models.JournalSource',JournalSource::class));

                $table->ulid('id')->primary();
                $table->string('reference_type', 50)->nullable();
                $table->string('reference_id', 36)->nullable();
                $table->foreignIdFor($transaction::class,'transaction_reference_id')->nullable()
                      ->index('jr_trx')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignIdFor($journal_source::class)->nullable()
                      ->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();
                $table->string('name', 255)->nullable();
                $table->timestamp('reported_at')->nullable();
                $table->string('status',50)->default($this->__table::STATUS_DRAFT)->nullable(false);
                $table->string('author_type',50)->nullable();
                $table->string('author_id',36)->nullable();
                $table->bigInteger('current_balance')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['reference_type', 'reference_id'], 'ref_joue');
                $table->index(['author_type','author_id'],'author_joue');
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained($table_name)
                    ->cascadeOnUpdate()->cascadeOnDelete();
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
