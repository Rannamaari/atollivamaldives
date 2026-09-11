<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table): void {
            $table->json('arabic_content')->nullable()->after('arabic_description');
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table): void {
            $table->dropColumn('arabic_content');
        });
    }
};
