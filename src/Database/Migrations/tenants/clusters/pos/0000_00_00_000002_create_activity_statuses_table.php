<?php

use Hanafalah\LaravelSupport\Models\Activity\Activity;
use Hanafalah\LaravelSupport\Models\Activity\ActivityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ActivityStatus', ActivityStatus::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->isNotTableExists(function(){
            $table_name = $this->__table->getTableName();
            Schema::create($table_name, function (Blueprint $table) {
                $activity = app(config('database.models.Activity', Activity::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($activity::class, 'activity_id')->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->unsignedBigInteger('status');
                $table->unsignedTinyInteger('active')->default(1);
                $table->text('message');
                $table->timestamps();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->__table->getTableName());
    }
};
