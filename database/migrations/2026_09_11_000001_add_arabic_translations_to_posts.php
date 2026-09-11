<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->string('arabic_title')->nullable()->after('body');
            $table->text('arabic_excerpt')->nullable()->after('arabic_title');
            $table->longText('arabic_body')->nullable()->after('arabic_excerpt');
            $table->string('arabic_seo_title')->nullable()->after('seo_title');
            $table->text('arabic_seo_description')->nullable()->after('arabic_seo_title');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn([
                'arabic_title',
                'arabic_excerpt',
                'arabic_body',
                'arabic_seo_title',
                'arabic_seo_description',
            ]);
        });
    }
};
