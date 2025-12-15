<?php

use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;
use Hanafalah\ModuleSupport\Models\DocumentReference;
use Hanafalah\ModuleSupport\Models\DocumentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.DocumentReference', DocumentReference::class));
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
                $document_type = app(config('database.models.DocumentType', DocumentType::class));

                $table->ulid('id')->primary();
                $table->string('name', 200)->nullable(false);
                $table->string('reference_type',50)->nullable(false);
                $table->string('reference_id',36)->nullable(false);
                $table->foreignIdFor($document_type::class)->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type','reference_id'],'ref_docRef');
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
