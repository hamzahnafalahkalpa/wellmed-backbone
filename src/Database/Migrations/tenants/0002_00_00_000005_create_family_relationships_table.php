<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModulePeople\Enums\People\Sex;
use Hanafalah\ModulePeople\Models\FamilyRelationship\FamilyRelationship;
use Hanafalah\ModulePeople\Models\FamilyRole;
use Hanafalah\ModulePeople\Models\People\People;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table,$__table_patient,$__table_people;

    public function __construct(){
        $this->__table = app(config('database.models.FamilyRelationship', FamilyRelationship::class));
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
                $people = app(config('database.models.People', People::class));
                $family_role = app(config('database.models.FamilyRole', FamilyRole::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($people,'people_id')
                      ->nullable()->after('id')->index()
                      ->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->string('name',50)->nullable(true);
                $table->string('phone',50)->nullable(true);
                $table->foreignIdFor($family_role)
                        ->after('id')->index()->constrained()
                        ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('sex',100)->comment(implode(', ',array_column(Sex::cases(), 'value')))->nullable(true);
                $table->string('reference_type',50)->nullable(true);
                $table->string('reference_id',36)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['reference_type','reference_id']);
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
