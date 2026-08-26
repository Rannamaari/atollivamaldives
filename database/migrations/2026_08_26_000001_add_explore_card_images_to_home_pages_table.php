<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('resorts_card_image')->nullable()->after('hero_image');
            $table->string('guesthouses_card_image')->nullable()->after('resorts_card_image');
            $table->string('city_hotels_card_image')->nullable()->after('guesthouses_card_image');
            $table->string('liveaboards_card_image')->nullable()->after('city_hotels_card_image');
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropColumn([
                'resorts_card_image',
                'guesthouses_card_image',
                'city_hotels_card_image',
                'liveaboards_card_image',
            ]);
        });
    }
};
