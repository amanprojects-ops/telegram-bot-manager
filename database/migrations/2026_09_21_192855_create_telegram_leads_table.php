<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_id')->unique();
            $table->bigInteger('telegram_user_id')->nullable()->index();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('budget')->nullable();
            $table->text('requirement')->nullable();
            $table->string('source')->default('telegram');
            $table->string('status')->default('new');
            $table->boolean('is_priority')->default(false);
            $table->boolean('is_contacted')->default(false);
            $table->integer('score')->default(0);
            $table->json('score_breakdown')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('followup_1_sent_at')->nullable();
            $table->timestamp('followup_2_sent_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('source');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_leads');
    }
};
