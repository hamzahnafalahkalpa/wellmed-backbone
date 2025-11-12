<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Enums\Coa\Status;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModulePayment\Models\Accounting\Coa;
use Hanafalah\ModulePayment\Models\Accounting\CoaType;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Coa', Coa::class));
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
                $coa_type = app(config('database.models.CoaType', CoaType::class));

                $table->ulid('id')->primary();
                $table->string('name', 100)->nullable();
                $table->string('code', 60)->nullable();
                $table->string('flag', 100)->nullable(false);
                $table->string('status', 10)->nullable(false)
                      ->default(Status::ACTIVE->value);
                $table->string('balance_type', 10)->nullable(true)->comment('DEBIT, CREDIT, MIX');
                $table->foreignIdFor($coa_type::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            
            Schema::table($table_name,function (Blueprint $table) use ($table_name){
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained($table_name)
                    ->onUpdate('cascade')->onDelete('restrict');
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
