<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\SatuSehat\Models\{
    SatuSehatLog
};

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.SatuSehatLog', SatuSehatLog::class));
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
                $table->string('name', 255)->nullable(false);
                $table->string('reference_type', 36)->nullable();
                $table->string('reference_id', 50)->nullable();
                $table->string('method', 10)->default('GET')->nullable(false);
                $table->string('env_type', 100)->nullable(false);
                $table->text('url')->nullable(false);
                $table->string('status',50)->nullable();
                $table->timestamp('expired_at')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
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
