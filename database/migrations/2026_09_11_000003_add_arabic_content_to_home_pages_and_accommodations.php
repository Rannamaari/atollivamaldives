<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->string('arabic_name')->nullable();
            $table->string('arabic_tagline')->nullable();
            $table->text('arabic_summary')->nullable();
            $table->longText('arabic_description')->nullable();
            $table->string('arabic_seo_title')->nullable();
            $table->text('arabic_seo_description')->nullable();
        });

        Schema::table('home_pages', function (Blueprint $table): void {
            $table->string('arabic_kicker')->nullable();
            $table->string('arabic_heading_line_one')->nullable();
            $table->string('arabic_heading_line_two')->nullable();
            $table->string('arabic_heading_emphasis')->nullable();
            $table->text('arabic_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', fn (Blueprint $table) => $table->dropColumn(['arabic_name', 'arabic_tagline', 'arabic_summary', 'arabic_description', 'arabic_seo_title', 'arabic_seo_description']));
        Schema::table('home_pages', fn (Blueprint $table) => $table->dropColumn(['arabic_kicker', 'arabic_heading_line_one', 'arabic_heading_line_two', 'arabic_heading_emphasis', 'arabic_description']));
    }
};
