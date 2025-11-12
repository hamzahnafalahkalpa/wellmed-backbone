<?php

use Hanafalah\ModuleItem\Models\ItemStuff;
use Hanafalah\ModuleItem\Models\Composition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Composition', Composition::class));
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
                $itemStuff = app(config('database.models.ItemStuff', ItemStuff::class));

                $table->ulid('id')->primary();
                $table->string('name')->nullable(false);
                $table->unsignedInteger('unit_scale')->default(0)->nullable(false);
                $table->foreignIdFor($itemStuff::class, 'unit_id')
                    ->nullable()->index()->constrained($itemStuff->getTable(), $itemStuff->getKeyName())
                    ->cascadeOnUpdate()->nullOnDelete();
                $table->string('unit_name', 100)->nullable(false);
                $table->json("props");
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
