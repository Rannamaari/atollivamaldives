<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Island extends Model
{
    use HasFactory;

    protected $fillable = ['atoll_id', 'name', 'slug', 'description', 'latitude', 'longitude', 'featured_image', 'status'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function atoll(): BelongsTo
    {
        return $this->belongsTo(Atoll::class);
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}
