<?php

use Hanafalah\ModuleExamination\Models\Examination\Assessment\Assessment;
use Hanafalah\ModulePatient\Models\EMR\ExaminationSummary;
use Hanafalah\ModuleExamination\Models\PatientSummary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\EMR\VisitExamination;
use Hanafalah\ModulePatient\Models\EMR\VisitRegistration;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Assessment', Assessment::class));
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
                $visit_registration  = app(config('database.models.VisitRegistration', VisitRegistration::class));

                $table->ulid("id")->primary();

                $table->string('examination_type', 50);
                $table->string('examination_id', 36);

                $table->foreignIdFor($visit_registration::class)
                    ->nullable()->index('vr_as')->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($examination_summary::class)
                    ->nullable()->index('es_as')->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($patient_summary::class)
                    ->nullable()->index('ps_as')->constrained('summaries')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('morph', 50)->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['examination_type', 'examination_id'], 've_morph_ass');
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained($table_name)
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
