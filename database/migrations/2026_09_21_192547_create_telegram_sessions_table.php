<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_user_id')->index();
            $table->bigInteger('chat_id')->index();
            $table->string('state')->default('idle');
            $table->json('payload')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['telegram_user_id', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_sessions');
    }
};
