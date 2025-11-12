<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleManufacture\Models\MaterialCategory;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.MaterialCategory', MaterialCategory::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();

        // Create the material_categories table if it doesn't exist
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('name', 255);
                $table->mediumText('note')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            
            // Add the parent_id column to the material_categories table
            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignId('parent_id')->nullable()->constrained($table_name)->nullOnDelete();
            });
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table_name = $this->__table->getTable();
        Schema::dropIfExists($table_name);
    }
};
