<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleItem\Models\{
    Item,
    ItemHasVariant,
    ItemStuff
};

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ItemHasVariant', ItemHasVariant::class));
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
                $item = app(config('database.models.Item', Item::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($item::class)->nullable(false)
                      ->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->string('variant_name',255)->nullable(false);
                $table->string('variant_label',255)->nullable(true);
                $table->string('variant_type',50)->nullable(true);
                $table->string('variant_id',36)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();


                $table->index(['variant_type', 'variant_id'], 'itv_variant');
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
