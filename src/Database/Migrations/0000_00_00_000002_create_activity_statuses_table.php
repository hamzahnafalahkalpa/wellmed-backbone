<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\MicroTenant\Models\Activity\CentralActivity;
use Hanafalah\MicroTenant\Models\Activity\CentralActivityStatus;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.CentralActivityStatus', CentralActivityStatus::class));
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
                $activity = app(config('database.models.CentralActivity', CentralActivity::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($activity::class, 'activity_id')->nullable()->index()
                    ->cascadeOnUpdate()->cascadeOnDelete();
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
