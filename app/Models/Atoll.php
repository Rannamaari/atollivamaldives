<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atoll extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'code', 'description', 'featured_image', 'status'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function islands(): HasMany
    {
        return $this->hasMany(Island::class);
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}
