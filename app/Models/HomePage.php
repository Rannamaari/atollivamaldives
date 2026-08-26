<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $fillable = [
        'name',
        'hero_image',
        'resorts_card_image',
        'guesthouses_card_image',
        'city_hotels_card_image',
        'liveaboards_card_image',
        'kicker',
        'heading_line_one',
        'heading_line_two',
        'heading_emphasis',
        'description',
        'explore_kicker',
        'explore_heading_line_one',
        'explore_heading_emphasis',
        'resorts_card_copy',
        'guesthouses_card_copy',
        'city_hotels_card_copy',
        'liveaboards_card_copy',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getHeroImageUrlAttribute(): string
    {
        return $this->hero_image
            ? asset('storage/'.$this->hero_image)
            : 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=2200&q=90';
    }

    public function getResortsCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->resorts_card_image);
    }

    public function getGuesthousesCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->guesthouses_card_image);
    }

    public function getCityHotelsCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->city_hotels_card_image);
    }

    public function getLiveaboardsCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->liveaboards_card_image);
    }

    protected function storageImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
