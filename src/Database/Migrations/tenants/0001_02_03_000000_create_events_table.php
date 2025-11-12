<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleEvent\Enums\Event\Status;
use Hanafalah\ModuleEvent\Models\Event\Event;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Event', Event::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('name', 255)->nullable(false);
                $table->string('event_code',50)->nullable();
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->unsignedTinyInteger('progress')->nullable(false)->default(0);
                $table->date('inited_at')->nullable();
                $table->date('started_at')->nullable();
                $table->date('ended_at')->nullable();
                $table->unsignedMediumInteger('total_day')->nullable();
                $table->string('status')->default(Status::DRAFT->value)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table($table_name,function(Blueprint $table) use ($table_name) {
               $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()
                    ->index()
                    ->constrained()
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
