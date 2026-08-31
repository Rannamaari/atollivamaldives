<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->string('social_title', 100)->nullable()->after('seo_title');
            $table->text('social_description')->nullable()->after('seo_description');
            $table->text('social_caption')->nullable()->after('social_description');
            $table->text('social_hashtags')->nullable()->after('social_caption');
            $table->string('social_image')->nullable()->after('social_hashtags');
            $table->string('generated_social_image')->nullable()->after('social_image');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->string('social_title', 100)->nullable()->after('seo_title');
            $table->text('social_description')->nullable()->after('seo_description');
            $table->text('social_caption')->nullable()->after('social_description');
            $table->text('social_hashtags')->nullable()->after('social_caption');
            $table->string('social_image')->nullable()->after('social_hashtags');
            $table->string('generated_social_image')->nullable()->after('social_image');
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->text('default_share_hashtags')->nullable()->after('default_og_image');
            $table->boolean('enable_share_tracking')->default(true)->after('default_share_hashtags');
            $table->boolean('enable_native_share')->default(true)->after('enable_share_tracking');
        });

        Schema::table('inquiries', function (Blueprint $table): void {
            $table->string('utm_source')->nullable()->after('source');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('utm_content')->nullable()->after('utm_campaign');
            $table->string('landing_page')->nullable()->after('utm_content');
        });

        Schema::create('social_share_events', function (Blueprint $table): void {
            $table->id();
            $table->morphs('shareable');
            $table->string('platform', 30)->index();
            $table->string('url')->nullable();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_share_events');

        Schema::table('inquiries', function (Blueprint $table): void {
            $table->dropColumn([
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'landing_page',
            ]);
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'default_share_hashtags',
                'enable_share_tracking',
                'enable_native_share',
            ]);
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn([
                'social_title',
                'social_description',
                'social_caption',
                'social_hashtags',
                'social_image',
                'generated_social_image',
            ]);
        });

        Schema::table('accommodations', function (Blueprint $table): void {
            $table->dropColumn([
                'social_title',
                'social_description',
                'social_caption',
                'social_hashtags',
                'social_image',
                'generated_social_image',
            ]);
        });
    }
};
