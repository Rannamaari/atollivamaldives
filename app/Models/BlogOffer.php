<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogOffer extends Model
{
    protected $fillable = [
        'eyebrow',
        'title',
        'description',
        'image',
        'button_text',
        'button_url',
        'target_categories',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'target_categories' => 'array',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! filled($this->image)) {
            return null;
        }

        return str_starts_with((string) $this->image, 'http')
            ? (string) $this->image
            : asset('storage/'.ltrim((string) $this->image, '/'));
    }
}
