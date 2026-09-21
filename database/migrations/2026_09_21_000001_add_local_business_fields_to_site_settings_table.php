<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('business_address_locality')->nullable()->after('business_address');
            $table->string('business_address_country_code', 2)->default('MV')->after('business_address_locality');
            $table->json('business_opening_days')->nullable()->after('company_description');
            $table->time('business_opening_time')->nullable()->after('business_opening_days');
            $table->time('business_closing_time')->nullable()->after('business_opening_time');
            $table->string('business_price_range')->nullable()->after('business_closing_time');
        });

        DB::table('site_settings')->update([
            'business_address' => 'M. Ithaamuiyge 1, Alimasmagu',
            'business_address_locality' => 'Male City',
            'business_address_country_code' => 'MV',
            'business_opening_days' => json_encode([
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
            ]),
            'business_opening_time' => '09:00:00',
            'business_closing_time' => '18:00:00',
            'business_price_range' => '$$',
            'facebook_url' => DB::raw("COALESCE(facebook_url, 'https://www.facebook.com/atollivamaldives')"),
            'instagram_url' => DB::raw("COALESCE(instagram_url, 'https://www.instagram.com/atollivamaldives/')"),
            'x_url' => DB::raw("COALESCE(x_url, 'https://x.com/myatolliva')"),
            'tiktok_url' => DB::raw("COALESCE(tiktok_url, 'https://www.tiktok.com/@atollivamaldives')"),
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'business_address_locality',
                'business_address_country_code',
                'business_opening_days',
                'business_opening_time',
                'business_closing_time',
                'business_price_range',
            ]);
        });
    }
};
