<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleExamination\Models\Form\Form;
use Hanafalah\ModuleExamination\Models\Form\FormHasSurvey;
use Hanafalah\ModuleExamination\Models\Form\Survey;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.FormHasSurvey', FormHasSurvey::class));
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
                $form = app(config('database.models.Form', Form::class));
                $survey = app(config('database.models.Survey', Survey::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($form::class)->nullable(false)->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor($survey::class)->nullable(false)->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
