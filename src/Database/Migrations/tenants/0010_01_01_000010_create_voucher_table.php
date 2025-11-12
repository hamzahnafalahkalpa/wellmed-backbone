<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Voucher', \Hanafalah\ModulePayment\Models\Price\Voucher::class));
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
                $table->ulid('id')->primary();
                $table->string('name');
                $table->string('status', 100);
                $table->string('benefit_target');
                $table->enum('benefit_format', [
                    $this->__table::BENEFIT_FORMAT_PERCENTAGE,
                    $this->__table::BENEFIT_FORMAT_AMOUNT
                ]);
                $table->unsignedBigInteger('benefit_value');
                $table->enum('benefit_type_value', [
                    $this->__table::BENEFIT_TYPE_MARKDOWN,
                    $this->__table::BENEFIT_TYPE_MARKUP,
                    $this->__table::BENEFIT_TYPE_DISCOUNT,
                    $this->__table::BENEFIT_TYPE_REPLACEMENT
                ]);
                $table->boolean('is_auto_implement')->default(0);
                $table->unsignedBigInteger('max_benefit_value')->nullable();
                $table->string('author_id', 50)->nullable(true);
                $table->string('author_type', 36)->nullable(true);
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
