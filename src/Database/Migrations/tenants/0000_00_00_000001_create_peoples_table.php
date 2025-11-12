<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePeople\Enums\People\BloodType;
use Hanafalah\ModulePeople\Enums\People\Sex;
use Hanafalah\ModulePeople\Models\Education;
use Hanafalah\ModulePeople\Models\MaritalStatus;
use Hanafalah\ModulePeople\Models\People\{
    People
};
use Hanafalah\ModulePeople\Models\Religion;
use Hanafalah\ModuleRegional\Models\Citizenship\Country;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.People', People::class));
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
                $country   = app(config('database.models.Country', Country::class));
                $education = app(config('database.models.Education', Education::class));
                $marital_status = app(config('database.models.MaritalStatus', MaritalStatus::class));
                $religion = app(config('database.models.Religion', Religion::class));

                $table->ulid('id')->primary();
                $table->string('uuid', 36)->nullable();
                $table->string('name', 100)->nullable(false);
                $table->string('first_name', 50)->nullable();
                $table->string('last_name', 50)->nullable();
                $table->date('dob')->nullable();
                $table->string('pob', 150)->nullable();
                $table->string('sex',100)->comment(implode(', ',array_column(Sex::cases(), 'value')))->nullable(false);
                $table->foreignIdFor($marital_status::class)->nullable()->index();
                $table->foreignIdFor($religion::class)->nullable()->index();

                $table->string('blood_type',100)->comment(implode(', ',array_column(BloodType::cases(), 'value')))->nullable();
                $table->string('mother_name', 50)->nullable();
                $table->string('father_name', 50)->nullable();
                $table->foreignIdFor($education::class,'last_education_id')->nullable()->index();

                $table->unsignedTinyInteger('total_children')->nullable();

                $table->foreignIdFor($country::class)->nullable()
                    ->index();

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
