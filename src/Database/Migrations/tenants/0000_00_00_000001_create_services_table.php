<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleService\Enums\Service\Status;
use Hanafalah\ModuleService\Models\Service;
use Hanafalah\ModuleService\Models\ServiceLabel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Service', Service::class));
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
                $service_label = app(config('database.models.ServiceLabel',ServiceLabel::class));

                $table->ulid('id')->primary();
                $table->string("name");
                $table->string("reference_id", 36);
                $table->string('reference_type', 50);
                $table->foreignIdFor($service_label::class)->nullable()->index()->constrained()
                      ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('status')->default(Status::ACTIVE->value)->nullable(false);
                $table->unsignedBigInteger('price')->default(0)->nullable(false);
                $table->unsignedBigInteger('cogs')->default(0)->nullable(false);
                $table->decimal('margin',10,2)->default(0)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_id', 'reference_type'], 'ref_service');
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
                    ->nullable()
                    ->index()->constrained()
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
