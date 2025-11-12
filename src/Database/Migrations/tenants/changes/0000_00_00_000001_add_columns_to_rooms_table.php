<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleMedicService\Models\MedicService;
use Hanafalah\ModuleMedicService\Models\ServiceCluster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleWarehouse\Models\Building\Building;
use Hanafalah\ModuleWarehouse\Models\Building\Room;

return new class extends Migration
{
    use NowYouSeeMe;
    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Room', Room::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotColumnExists('medic_service_id',function() use ($table_name){
            Schema::table($table_name, function (Blueprint $table) {
                $medic_service = app(config('database.models.MedicService',MedicService::class));
                $service_cluster = app(config('database.models.ServiceCluster',ServiceCluster::class));

                $table->foreignIdFor($medic_service::class)->nullable()
                      ->after('name')
                      ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($service_cluster::class)->nullable()
                      ->after('medic_service_id')
                      ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
