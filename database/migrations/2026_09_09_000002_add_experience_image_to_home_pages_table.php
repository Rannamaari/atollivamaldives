<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table): void {
            $table->string('experience_image')->nullable()->after('hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table): void {
            $table->dropColumn('experience_image');
        });
    }
};
