<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;
use Hanafalah\ModuleEvent\Models\Program;
use Hanafalah\ModuleEvent\Models\ProgramCategory;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Program', Program::class));
    }   

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $program_category = app(config('database.models.ProgramCategory',ProgramCategory::class));

                $table->ulid('id')->primary();
                $table->string('program_code',50)->nullable();
                $table->string('name',255)->nullable(false);
                $table->string('flag',100)->nullable(false);
                $table->foreignIdFor($program_category::class)->nullable(true)
                        ->index()->constrained()->restrictOnDelete()
                        ->cascadeOnUpdate();
                $table->unsignedBigInteger('nominal')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor(get_class($this->__table), 'parent_id')
                      ->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTable());
    }
};
