<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hero_image')->nullable();
            $table->string('kicker')->default('YOUR MALDIVES, THOUGHTFULLY PLANNED');
            $table->string('heading_line_one')->default('Find your way');
            $table->string('heading_line_two')->default('to');
            $table->string('heading_emphasis')->default('paradise.');
            $table->text('description')->default('Handpicked stays, remarkable ocean journeys, and honest local advice-all in one place.');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        DB::table('home_pages')->insert([
            'name' => 'Default Homepage Hero',
            'hero_image' => DB::table('site_settings')->value('hero_image'),
            'kicker' => 'YOUR MALDIVES, THOUGHTFULLY PLANNED',
            'heading_line_one' => 'Find your way',
            'heading_line_two' => 'to',
            'heading_emphasis' => 'paradise.',
            'description' => 'Handpicked stays, remarkable ocean journeys, and honest local advice-all in one place.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_pages');
    }
};
