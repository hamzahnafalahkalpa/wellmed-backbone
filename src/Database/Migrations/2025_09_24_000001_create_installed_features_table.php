<?php

use Hanafalah\LaravelFeature\Models\InstalledFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\LaravelFeature\Models\MasterFeature as ModelsMasterFeature;
use Hanafalah\LaravelFeature\Models\Version;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.InstalledFeature', InstalledFeature::class));
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
                $masterFeature  = app(config('database.models.MasterFeature', ModelsMasterFeature::class));
                $version  = app(config('database.models.Version', Version::class));

                $table->ulid('id')->primary();
                $table->string('model_type', 50)->nullable(false);
                $table->string('model_id', 36)->nullable(false);
                $table->foreignIdFor($masterFeature::class)->nullable()
                    ->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();
                $table->foreignIdFor($version::class)->nullable(false)
                    ->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();
                $table->unsignedTinyInteger('current')->default(1)->nullable(false);
                $table->unsignedTinyInteger('batch')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['model_type', 'model_id'], 'feature_model');
            });
        });

        $this->isNotColumnExists('parent_id',function() use ($table_name){
            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
                    ->after('id')->nullable()->index()->constrained($table_name)
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
