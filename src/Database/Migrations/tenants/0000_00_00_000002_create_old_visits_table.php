<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\Patient\{
    Patient,
};
use Hanafalah\ModulePatient\Models\EMR\OldVisit;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;
    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.OldVisit', OldVisit::class));
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
                $patient = app(config('database.models.Patient', Patient::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($patient)->nullable()->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
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
