<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleEmployee\Models\Attendence\{
    ShiftHasSchedule,
    ShiftSchedule
};
use Hanafalah\ModuleEmployee\Models\Attendence\Shift;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ShiftHasSchedule', ShiftHasSchedule::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $shift = app(config('database.models.Shift', Shift::class));
                $shift_schedule = app(config('database.models.ShiftSchedule', ShiftSchedule::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($shift::class)->nullable(false)->index()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor($shift_schedule::class)->nullable(false)->index()->cascadeOnUpdate()->cascadeOnDelete();
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
