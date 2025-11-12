<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\EMR\VisitExamination;
use Hanafalah\ModuleExamination\Models\Examination\ExaminationTreatment;
use Hanafalah\ModuleExamination\Models\PatientSummary;
use Hanafalah\ModuleTreatment\Models\Treatment\Treatment;
use Hanafalah\ModulePatient\Models\EMR\ExaminationSummary;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ExaminationTreatment', ExaminationTreatment::class));
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
                $visit_examination   = app(config('database.models.VisitExamination', VisitExamination::class));
                $examination_summary = app(config('database.models.ExaminationSummary', ExaminationSummary::class));
                $patient_summary     = app(config('database.models.PatientSummary', PatientSummary::class));
                $treatment           = app(config('database.models.Treatment', Treatment::class));

                $table->ulid("id")->primary();
                $table->string('name')->nullable(false);

                $table->foreignIdFor($visit_examination::class)
                    ->nullable()->constrained($visit_examination->getTable(), $visit_examination->getKeyName(), 've_et')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($examination_summary::class)
                    ->nullable()->constrained($examination_summary->getTable(), $examination_summary->getKeyName(), 'es_et')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($patient_summary::class)
                    ->nullable()->constrained('summaries', $patient_summary->getKeyName(), 'ps_et')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($treatment::class,'treatment_id')
                    ->nullable()->constrained($treatment->getTable(), $treatment->getKeyName(), 'tr_et')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('reference_type', 50)->nullable();
                $table->string('reference_id', 36)->nullable();
                $table->unsignedBigInteger('qty')->default(1)->nullable();
                $table->unsignedBigInteger('price')->nullable();

                $table->json('props')->nullable();

                $table->index(['reference_type', 'reference_id'], 'et_ref');

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
