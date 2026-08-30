<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('site_name')->nullable()->after('hero_image');
            $table->string('default_meta_title')->nullable()->after('site_name');
            $table->text('default_meta_description')->nullable()->after('default_meta_title');
            $table->string('default_og_image')->nullable()->after('default_meta_description');
            $table->string('business_logo')->nullable()->after('default_og_image');
            $table->string('business_email')->nullable()->after('business_logo');
            $table->string('business_phone')->nullable()->after('business_email');
            $table->string('business_secondary_phone')->nullable()->after('business_phone');
            $table->text('business_address')->nullable()->after('business_secondary_phone');
            $table->text('company_description')->nullable()->after('business_address');
            $table->string('facebook_url')->nullable()->after('company_description');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('x_url')->nullable()->after('instagram_url');
            $table->string('tiktok_url')->nullable()->after('x_url');
            $table->boolean('default_robots_index')->default(true)->after('tiktok_url');
            $table->boolean('default_robots_follow')->default(true)->after('default_robots_index');
            $table->string('google_analytics_id')->nullable()->after('default_robots_follow');
            $table->string('google_tag_manager_id')->nullable()->after('google_analytics_id');
            $table->string('google_search_console_verification')->nullable()->after('google_tag_manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_name',
                'default_meta_title',
                'default_meta_description',
                'default_og_image',
                'business_logo',
                'business_email',
                'business_phone',
                'business_secondary_phone',
                'business_address',
                'company_description',
                'facebook_url',
                'instagram_url',
                'x_url',
                'tiktok_url',
                'default_robots_index',
                'default_robots_follow',
                'google_analytics_id',
                'google_tag_manager_id',
                'google_search_console_verification',
            ]);
        });
    }
};
