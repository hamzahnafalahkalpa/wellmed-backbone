<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\LaravelSupport\Models\Elasticsearch\ElasticsearchLog;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ElasticsearchLog', ElasticsearchLog::class));
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
                $table->ulid("id")->primary();
                $table->string('name',255)->nullable();
                $table->string('reference_type',50)->nullable();
                $table->string('reference_id',36)->nullable();
                $table->timestamp('synced_at')->nullabl();
                $table->json('props')->nullable();
                $table->index(['reference_type', 'reference_id'], 'es_ref_fk');
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
