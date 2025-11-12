<?php

use Hanafalah\ModuleService\Models\{
    Service,
    ServiceItem,
};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ServiceItem', ServiceItem::class));
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
                $service = app(config('database.models.Service', Service::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($service::class)->index()->constrained()
                    ->restrictOnDelete()->cascadeOnUpdate();

                $table->string("reference_id", 36);
                $table->string('reference_type', 50);
                $table->string('name')->nullable();
                $table->unsignedBigInteger('price')->nullable()->default(0);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_id', 'reference_type'], 'ref_si');
                $table->index(['reference_type'], 'ref_type_si');
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
                    ->nullable()->after('id')
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
