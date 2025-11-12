<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleProcurement\Models\{
    PurchaseRequest,
    Purchasing
};

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PurchaseRequest', PurchaseRequest::class));
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
                $purchasing    = app(config('database.models.Purchasing', Purchasing::class));

                $table->ulid('id')->primary();
                $table->string('name', 255)->nullable(false);
                $table->string('approver_type',50)->nullable();
                $table->string('approver_id',36)->nullable();
                $table->date('estimate_used_at')->nullable();
                $table->foreignIdFor($purchasing, 'purchasing_id')->nullable()
                      ->constrained()->cascadeOnUpdate()->nullOnDelete();

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
