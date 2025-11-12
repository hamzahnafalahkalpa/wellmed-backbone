<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleManufacture\Models\Material;
use Hanafalah\ModuleManufacture\Models\MaterialCategory;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Material', Material::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $material_category = app(config('database.models.MaterialCategory',MaterialCategory::class));

                $table->ulid('id')->primary();
                $table->string('material_code')->nullable();
                $table->string('name',255)->nullable();
                $table->string('flag',100)->nullable();

                $table->foreignIdFor($material_category::class)
                    ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

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
