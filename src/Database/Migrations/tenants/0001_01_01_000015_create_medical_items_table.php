<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleMedicalItem\Models\MedicalItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use NowYouSeeMe;
    
    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.MedicalItem', MedicalItem::class));
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
                $table->ulid('id')->primary();
                $table->string('medical_item_code', 50)->nullable();
                $table->string('registration_no', 100)->nullable();
                $table->string('name');
                $table->string('reference_id', 36);
                $table->string('reference_type', 50);
                $table->boolean('is_pom')->nullable(false)->default(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_id', 'reference_type'], 'medical_ref');
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
