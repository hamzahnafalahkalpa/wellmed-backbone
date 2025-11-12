<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\LaravelPermission\Enums\Permission\Type;
use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\LaravelPermission\Models\Permission\Permission;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Permission', Permission::class));
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('name', 200)->nullable(false);
                $table->string('alias', 255)->nullable(false);
                $table->string('type', 100)->comment(implode(', ',array_column(Type::cases(), 'value')))->default(Type::PERMISSION->value);
                $table->boolean('visibility')->default(1);
                $table->unsignedMediumInteger('ordering')->default(1)->nullable(false);
                $table->string('guard_name', 50)->nullable()->index();
                $table->json('props')->nullable();
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained($table_name)
                    ->cascadeOnUpdate()->restrictOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTableName());
    }
};
