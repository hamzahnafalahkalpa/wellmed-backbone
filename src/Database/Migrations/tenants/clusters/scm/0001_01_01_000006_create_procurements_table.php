<?php

use Hanafalah\ModuleProcurement\Models\Procurement;
use Hanafalah\ModuleProcurement\Models\PurchaseLabel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Procurement', Procurement::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (! $this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $purchase_label = app(config('database.models.PurchaseLabel',PurchaseLabel::class));

                $table->ulid('id')->primary();
                $table->string('name',255)->nullable();
                $table->string('reference_type', 50)->nullable();
                $table->string('reference_id', 36)->nullable();
                $table->string('author_type', 50)->nullable();
                $table->string('author_id', 36)->nullable();
                $table->string('warehouse_type', 50)->nullable();
                $table->string('warehouse_id', 36)->nullable();
                $table->string('sender_type', 50)->nullable();
                $table->string('sender_id', 36)->nullable();
                $table->string('status', 50)->nullable(false);
                $table->foreignIdFor($purchase_label::class)->nullable()->index()->constrained()
                      ->cascadeOnUpdate()->restrictOnDelete();
                $table->unsignedBigInteger('total_cogs')->default(0)->nullable(false);
                $table->timestamp('reported_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['warehouse_type', 'warehouse_id'], 'fk_prc_warehouse');
                $table->index(['sender_type', 'sender_id'], 'fk_prc_sender');
                $table->index(['author_type', 'author_id'], 'fk_prc_author');
                $table->index(['reference_type', 'reference_id'], 'fk_prc_reference');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTable());
    }
};
