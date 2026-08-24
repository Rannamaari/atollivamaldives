<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveaboardPage extends Model
{
    protected $fillable = [
        'hero_image',
        'eyebrow',
        'title',
        'intro',
        'body',
        'gallery_images',
        'contact_heading',
        'contact_text',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'eyebrow' => 'LIVEABOARD MALDIVES',
            'title' => 'Charter a beautiful liveaboard and make the ocean your home.',
            'intro' => 'Sail with your friends and family, wake up to open sea views, and explore the Maldives through a private journey built around comfort, freedom and unforgettable moments.',
            'body' => 'Whether you are planning a diving escape, a family adventure or a special celebration on the water, Micro Travel can help you choose the right liveaboard, shape the right route and organise the details that make the experience feel effortless from beginning to end.',
            'contact_heading' => 'Plan your liveaboard journey',
            'contact_text' => 'Tell us your preferred dates, group size and the kind of experience you want. We will help you find the right liveaboard and build a memorable Maldives journey at sea.',
        ]);
    }

    public function getHeroImageUrlAttribute(): string
    {
        return $this->hero_image
            ? asset('storage/' . $this->hero_image)
            : 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1800&q=85';
    }
}
