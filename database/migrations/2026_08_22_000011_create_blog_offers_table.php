<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_offers', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->default('Explore offer');
            $table->string('button_url')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('blog_offers')->insert([
            [
                'eyebrow' => 'Featured resort escape',
                'title' => 'Placeholder Resort Offer',
                'description' => 'Use this space to promote a handpicked resort with a strong call to action after each blog article.',
                'image' => 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?auto=format&fit=crop&w=1400&q=85',
                'button_text' => 'View resort',
                'button_url' => '/stays',
                'active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'eyebrow' => 'Special Maldives package',
                'title' => 'Placeholder Holiday Package',
                'description' => 'Replace this with your own package, honeymoon deal, diving plan, or seasonal promotion from the admin panel.',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=85',
                'button_text' => 'See package',
                'button_url' => '/liveaboards',
                'active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_offers');
    }
};
