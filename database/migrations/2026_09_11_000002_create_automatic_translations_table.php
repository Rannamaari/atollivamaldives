<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automatic_translations', function (Blueprint $table): void {
            $table->id();
            $table->string('source_hash', 64)->unique();
            $table->string('source_locale', 10);
            $table->string('target_locale', 10);
            $table->longText('source_text');
            $table->longText('translated_text');
            $table->string('provider');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automatic_translations');
    }
};
