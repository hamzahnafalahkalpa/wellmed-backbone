<?php

use Hanafalah\ModuleExamination\Models\Examination\PatientIllness;
use Hanafalah\ModulePatient\Models\EMR\ExaminationSummary;
use Hanafalah\ModuleExamination\Models\PatientSummary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\Patient\Patient;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PatientIllness', PatientIllness::class));
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
                $examination_summary = app(config('database.models.ExaminationSummary', ExaminationSummary::class));
                $patient_summary     = app(config('database.models.PatientSummary', PatientSummary::class));
                $patient             = app(config('database.models.Patient', Patient::class));

                $table->ulid("id")->primary();

                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->string('name')->nullable(false);

                $table->foreignIdFor($patient::class)
                    ->nullable()->index('pat_pi')->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($examination_summary::class)
                    ->nullable()->index('es_pi')->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($patient_summary::class)
                    ->nullable()->index('ps_pi')->constrained('summaries')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('disease_type', 50)->nullable(false);
                $table->string('disease_id', 36)->nullable(false);
                $table->string('disease_name')->nullable(false);

                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id'], 'ref_pi');
                $table->index(['disease_type', 'disease_id'], 'disease_pi');
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table::class, 'classification_disease_id')
                    ->after('disease_name')->nullable()->index()
                    ->constrained($table_name, 'id', 'cd_pi')
                    ->cascadeOnUpdate()->restrictOnDelete();
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
