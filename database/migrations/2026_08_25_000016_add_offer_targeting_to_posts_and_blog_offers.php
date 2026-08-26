<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_offers', function (Blueprint $table) {
            $table->json('target_categories')->nullable()->after('button_url');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('blog_offer_id')
                ->nullable()
                ->after('category')
                ->constrained('blog_offers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blog_offer_id');
        });

        Schema::table('blog_offers', function (Blueprint $table) {
            $table->dropColumn('target_categories');
        });
    }
};
