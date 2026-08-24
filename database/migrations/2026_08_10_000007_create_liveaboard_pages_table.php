<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('liveaboard_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image')->nullable();
            $table->string('eyebrow')->default('LIVEABOARD MALDIVES');
            $table->string('title')->default('Charter a beautiful liveaboard and make the ocean your home.');
            $table->text('intro')->nullable();
            $table->longText('body')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('contact_heading')->default('Plan your liveaboard journey');
            $table->text('contact_text')->nullable();
            $table->timestamps();
        });

        DB::table('liveaboard_pages')->insert([
            'eyebrow' => 'LIVEABOARD MALDIVES',
            'title' => 'Charter a beautiful liveaboard and make the ocean your home.',
            'intro' => 'Sail with your friends and family, wake up to open sea views, and explore the Maldives through a private journey built around comfort, freedom and unforgettable moments.',
            'body' => 'Whether you are planning a diving escape, a family adventure or a special celebration on the water, Micro Travel can help you choose the right liveaboard, shape the right route and organise the details that make the experience feel effortless from beginning to end.',
            'contact_heading' => 'Plan your liveaboard journey',
            'contact_text' => 'Tell us your preferred dates, group size and the kind of experience you want. We will help you find the right liveaboard and build a memorable Maldives journey at sea.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('liveaboard_pages');
    }
};
