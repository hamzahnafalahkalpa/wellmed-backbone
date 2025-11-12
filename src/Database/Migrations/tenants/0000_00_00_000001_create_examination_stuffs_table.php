<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleExamination\Models\ExaminationStuff;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ExaminationStuff', ExaminationStuff::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('flag',100)->nullable(false);
                $table->string('label',100)->nullable(true);
                $table->string('name', 100)->nullable(false);
                $table->string('status', 100)->nullable(true);
                $table->unsignedInteger('ordering')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(["flag"], "ex_st_flag");
                $table->index(['flag','label'], "ex_st_lbl_flag");

            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after($this->__table->getKeyName())
                    ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists($this->__table->getTable());
    }
};
