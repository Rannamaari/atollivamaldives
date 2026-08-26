<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('explore_kicker')->default('EXPLORE MALDIVES')->after('description');
            $table->string('explore_heading_line_one')->default('Browse our')->after('explore_kicker');
            $table->string('explore_heading_emphasis')->default('travel products.')->after('explore_heading_line_one');
            $table->text('resorts_card_copy')->nullable()->after('resorts_card_image');
            $table->text('guesthouses_card_copy')->nullable()->after('guesthouses_card_image');
            $table->text('city_hotels_card_copy')->nullable()->after('city_hotels_card_image');
            $table->text('liveaboards_card_copy')->nullable()->after('liveaboards_card_image');
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropColumn([
                'explore_kicker',
                'explore_heading_line_one',
                'explore_heading_emphasis',
                'resorts_card_copy',
                'guesthouses_card_copy',
                'city_hotels_card_copy',
                'liveaboards_card_copy',
            ]);
        });
    }
};
