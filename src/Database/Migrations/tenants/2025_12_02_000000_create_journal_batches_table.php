<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePayment\Models\{
    JournalBatch
};

return new class extends Migration
{
    use NowYouSeeMe;

    

    public function __construct()
    {
        $this->__table = app(config('database.models.JournalBatch', JournalBatch::class));
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
                $table->string('reference_type')->nullable();
                $table->ulid('reference_id')->nullable();
                $table->timestamp('reported_at')->nullable();
                $table->text('note')->nullable();
                $table->string('status')->nullable();
                $table->string('author_type')->nullable();
                $table->ulid('author_id')->nullable();
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
