<?php

use Hanafalah\ModuleExamination\Models\Form\Form;
use Hanafalah\ModuleExamination\Models\Form\Screening;
use Hanafalah\ModuleExamination\Models\Form\ScreeningHasForm;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ScreeningHasForm', ScreeningHasForm::class));
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
                $screening = app(config('database.models.Screening', Screening::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($form::class)->nullable(false)->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->foreignIdFor($screening::class, 'screening_id')->nullable(false)->index()
                    ->constrained($screening->getTable(), 'id')
                    ->cascadeOnUpdate()->cascadeOnDelete();
                $table->json('props');
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
