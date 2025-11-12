<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\EMR\VisitExamination;
use Hanafalah\ModuleExamination\Models\Examination\Prescription;
use Hanafalah\ModuleExamination\Models\PatientSummary;
use Hanafalah\ModuleTreatment\Models\Treatment\Treatment;
use Hanafalah\ModulePatient\Models\EMR\ExaminationSummary;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Prescription', Prescription::class));
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

                $table->ulid("id")->primary();
                $table->string('name')->nullable(false);

                $table->foreignIdFor($visit_examination::class)
                    ->nullable()->constrained($visit_examination->getTable(), $visit_examination->getKeyName(), 've_pre')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($examination_summary::class)
                    ->nullable()->constrained($examination_summary->getTable(), $examination_summary->getKeyName(), 'es_pre')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($patient_summary::class)
                    ->nullable()->constrained('summaries', $patient_summary->getKeyName(), 'ps_pre')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->unsignedBigInteger('cogs')->default(0);
                $table->unsignedBigInteger('price')->default(0);
                $table->decimal('qty', 14, 6)->default(0)->nullable(false);

                $table->string('reference_type', 50)->nullable();
                $table->string('reference_id', 36)->nullable();

                $table->json('props')->nullable();

                $table->index(['reference_type', 'reference_id'], 'pre_ref');

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
