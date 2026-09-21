<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brochure_downloads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_user_id')->index();
            $table->foreignId('brochure_id')->constrained()->cascadeOnDelete();
            $table->string('telegram_file_id')->nullable();
            $table->timestamp('downloaded_at')->useCurrent();

            $table->index(['telegram_user_id', 'brochure_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brochure_downloads');
    }
};
