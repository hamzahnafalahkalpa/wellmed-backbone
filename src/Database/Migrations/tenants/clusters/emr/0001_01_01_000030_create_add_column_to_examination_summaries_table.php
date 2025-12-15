<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModulePatient\Models\EMR\ExaminationSummary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\Patient\Patient;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ExaminationSummary', ExaminationSummary::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $patient = app(config('database.models.Patient', Patient::class));
        $this->isNotColumnExists($patient->getForeignKey(),function() use ($patient){
            $table_name = $this->__table->getTable();
            Schema::table($table_name, function (Blueprint $table) use ($patient) {
                $table->foreignIdFor($patient::class)->after('parent_id')->index()
                    ->nullable(false)->cascadeOnUpdate()
                    ->cascadeOnDelete();
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
