<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleMedicalItem\Models\Medicine;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleMedicalItem\Models\DosageForm;
use Hanafalah\ModuleMedicalItem\Models\PackageForm;
use Hanafalah\ModuleMedicalItem\Models\TherapeuticClass;
use Hanafalah\ModuleMedicalItem\Models\Trademark;
use Hanafalah\ModuleMedicalItem\Models\UsageLocation;
use Hanafalah\ModuleMedicalItem\Models\UsageRoute;

return new class extends Migration
{
      use NowYouSeeMe;

      private $__table;

      public function __construct()
      {
            $this->__table = app(config('database.models.Medicine', Medicine::class));
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
                        $dosageForm       = app(config('database.models.DosageForm', DosageForm::class));
                        $usageLocation    = app(config('database.models.UsageLocation', UsageLocation::class));
                        $usageRoute       = app(config('database.models.UsageRoute', UsageRoute::class));
                        $therapeuticClass = app(config('database.models.TherapeuticClass', TherapeuticClass::class));
                        $packageForm      = app(config('database.models.PackageForm', PackageForm::class));
                        $trademark        = app(config('database.models.Trademark', Trademark::class));

                        $table->ulid('id')->primary();
                        $table->string('name');
                        $table->string('status')->nullable();
                        $table->string('acronym')->nullable();
                        $table->boolean('is_lasa')->default(false)->nullable(false);
                        $table->boolean('is_antibiotic')->default(false)->nullable(false);
                        $table->boolean('is_high_alert')->default(false)->nullable(false);
                        $table->boolean('is_narcotic')->default(false)->nullable(false);

                        $table->foreignIdFor($usageLocation::class, 'usage_location_id')
                              ->nullable()->index()->constrained($usageLocation->getTable(), $usageLocation->getKeyName())
                              ->cascadeOnUpdate()->nullOnDelete();

                        $table->foreignIdFor($usageRoute::class, 'usage_route_id')
                              ->nullable()->index()->constrained($usageRoute->getTable(), $usageRoute->getKeyName())
                              ->cascadeOnUpdate()->nullOnDelete();

                        $table->foreignIdFor($therapeuticClass::class, 'therapeutic_class_id')
                              ->nullable()->index()->constrained($therapeuticClass->getTable(), $therapeuticClass->getKeyName())
                              ->cascadeOnUpdate()->nullOnDelete();

                        $table->foreignIdFor($dosageForm::class, 'dosage_form_id')
                              ->nullable()->index()->constrained($dosageForm->getTable(), $dosageForm->getKeyName())
                              ->cascadeOnUpdate()->nullOnDelete();

                        $table->foreignIdFor($packageForm::class, 'package_form_id')
                              ->nullable()->index()->constrained($packageForm->getTable(), $packageForm->getKeyName())
                              ->cascadeOnUpdate()->nullOnDelete();

                        $table->foreignIdFor($trademark::class, 'trademark_id')
                              ->nullable()->index()->constrained($trademark->getTable(), $trademark->getKeyName())
                              ->cascadeOnUpdate()->nullOnDelete();

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
