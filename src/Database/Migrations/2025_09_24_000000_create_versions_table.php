<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\LaravelFeature\Models\{
    MasterFeature,
    Version
};

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Version', Version::class));
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
                $master_feature_model = app(config('database.models.MasterFeature', MasterFeature::class));

                $table->ulid('id')->primary();
                $table->string('name', 255)->nullable(false);
                $table->string('version', 50)->nullable(false);
                $table->foreignIdFor($master_feature_model::class)->nullable(false)
                      ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->unsignedBigInteger('price')->nullable(true);
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
