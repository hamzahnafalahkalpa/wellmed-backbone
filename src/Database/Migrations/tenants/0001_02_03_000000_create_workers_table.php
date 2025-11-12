<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;
use Hanafalah\ModuleEvent\Models\Event\Event;
use Hanafalah\ModuleEvent\Models\Member\Worker;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Worker', Worker::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $event = app(config('database.models.Event',Event::class));

                $table->ulid('id')->primary();
                $table->string('name',200)->nullable(false);
                $table->foreignIdFor($event::class)->nullable(false)
                        ->index()->constrained()
                        ->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('reference_type',50)->nullable(true);
                $table->string('reference_id',36)->nullable(true);
                $table->string('occupation_type',50)->nullable(true);
                $table->string('occupation_id',36)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
