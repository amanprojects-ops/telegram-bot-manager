<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('update_id')->unique();
            $table->bigInteger('telegram_user_id')->nullable()->index();
            $table->string('event_type')->index();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->string('status')->default('received');
            $table->text('error')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_logs');
    }
};
