<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $fillable = [
        'name',
        'hero_image',
        'kicker',
        'heading_line_one',
        'heading_line_two',
        'heading_emphasis',
        'description',
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
            ? asset('storage/' . $this->hero_image)
            : 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=2200&q=90';
    }
}
