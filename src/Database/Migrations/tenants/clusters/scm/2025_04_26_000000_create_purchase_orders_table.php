<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleFunding\Models\Funding\Funding;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleProcurement\Models\{
    Purchasing,
    PurchaseOrder
};

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PurchaseOrder', PurchaseOrder::class));
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
                $funding       = app(config('database.models.Funding', Funding::class));
                $purchasing    = app(config('database.models.Purchasing', Purchasing::class));

                $table->ulid('id')->primary();
                $table->string('name')->nullable();

                $table->foreignIdFor($funding::class)->nullable()->index()
                      ->constrained()->restrictOnDelete()->cascadeOnUpdate();

                $table->string('supplier_type',50)->nullable();
                $table->string('supplier_id',36)->nullable();

                $table->foreignIdFor($purchasing::class)->nullable()->index()
                      ->constrained()->restrictOnDelete()->cascadeOnUpdate();

                $table->string('flag',100)->nullable();
                $table->string('phone',50)->nullable();
                $table->text('received_address')->nullable();
                      
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['supplier_type', 'supplier_id'], 'supplier_ref');
            });

            
            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained($table_name)
                    ->cascadeOnUpdate()->restrictOnDelete();
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
