<?php

use App\Models\User;
use Hanafalah\ModuleNotification\Models\Notification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\MicroTenant\Concerns\Tenant\NowYouSeeMe;

    public function __construct()
    {
        $this->__table = app(config('database.models.Notification', Notification::class));
    }

    /**
     * This migration targets the tenant database's public schema.
     * The notifications table stores per-tenant notifications.
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        $this->isNotTableExists(function() use ($table_name){
            Schema::create($table_name, function (Blueprint $table) {
                $user = app(config('database.models.User',User::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($user)->nullable(false)->index();
                $table->string('icon')->nullable();
                $table->string('title');
                $table->text('content');
                $table->string('type')->nullable();
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'read_at']);
                $table->index('created_at');
            });
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTable());
    }
};
