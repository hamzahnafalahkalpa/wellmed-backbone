<?php

use Hanafalah\ModuleEncoding\Models\Encoding\Encoding;
use Projects\WellmedBackbone\Models\WellmedModelHasEncoding;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Projects\WellmedBackbone\Models\WellmedEncoding;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.WellmedModelHasEncoding', WellmedModelHasEncoding::class));
    }

    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $encoding   = app(config('database.models.Encoding', WellmedEncoding::class));

                $table->ulid('id')->primary();
                $table->string('reference_id', 36);
                $table->string('reference_type', 60);
                $table->string('value')->nullable();
                $table->foreignIdFor($encoding::class, 'encoding_id')
                      ->nullable()->cascadeOnUpdate()->restrictOnDelete();
                $table->json('props')->nullable();
                $table->timestamps();

                $table->index(["reference_id", "reference_type"], 'wellmed_encoding_ref');
                $table->index(["reference_id", "reference_type", "encoding_id"], 'wellmed_encoding_keys');
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
