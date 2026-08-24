<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['hero_image'];

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public function getHeroImageUrlAttribute(): string
    {
        return $this->hero_image
            ? asset('storage/' . $this->hero_image)
            : 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=2200&q=90';
    }
}
