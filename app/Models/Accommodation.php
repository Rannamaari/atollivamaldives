<?php

namespace App\Models;

use App\Enums\AccommodationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'property_subtype', 'status', 'name', 'previous_name', 'aliases', 'slug', 'tagline', 'summary', 'description', 'island', 'island_id', 'atoll', 'atoll_id', 'city', 'country', 'address', 'official_website', 'source_url', 'latitude', 'longitude', 'price_from', 'currency', 'price_unit', 'rating', 'images', 'featured_image', 'amenities', 'featured', 'verified', 'published', 'vessel_name', 'vessel_type', 'cabins', 'maximum_guests', 'length_meters', 'cruising_speed_knots', 'diving_available', 'surfing_available', 'snorkeling_available', 'nitrox_available', 'dhoni_available', 'jacuzzi', 'spa', 'restaurant', 'bar', 'wifi', 'departure_port', 'typical_route', 'typical_trip_length', 'minimum_nights', 'check_in_time', 'check_out_time', 'airport_distance', 'transfer_duration', 'transfer_notes', 'house_rules', 'cancellation_policy', 'sort_order', 'seo_title', 'seo_description'];

    protected function casts(): array
    {
        return ['type' => AccommodationType::class, 'aliases' => 'array', 'images' => 'array', 'amenities' => 'array', 'featured' => 'boolean', 'verified' => 'boolean', 'published' => 'boolean', 'price_from' => 'decimal:2', 'rating' => 'decimal:1', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'length_meters' => 'decimal:2', 'cruising_speed_knots' => 'decimal:2', 'diving_available' => 'boolean', 'surfing_available' => 'boolean', 'snorkeling_available' => 'boolean', 'nitrox_available' => 'boolean', 'dhoni_available' => 'boolean', 'jacuzzi' => 'boolean', 'spa' => 'boolean', 'restaurant' => 'boolean', 'bar' => 'boolean', 'wifi' => 'boolean', 'check_in_time' => 'datetime:H:i', 'check_out_time' => 'datetime:H:i'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getCoverImageAttribute(): string
    {
        return $this->featured_image ?: ($this->images[0] ?? match ($this->type) {
            AccommodationType::Guesthouse => 'placeholders/guesthouse-placeholder.svg',AccommodationType::CityHotel => 'placeholders/city-hotel-placeholder.svg',AccommodationType::Liveaboard => 'placeholders/liveaboard-placeholder.svg',default => 'placeholders/resort-placeholder.svg'
        });
    }

    public function atollRelation(): BelongsTo
    {
        return $this->belongsTo(Atoll::class, 'atoll_id');
    }

    public function islandRelation(): BelongsTo
    {
        return $this->belongsTo(Island::class, 'island_id');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(AccommodationImage::class)->orderBy('sort_order');
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'accommodation_facility');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class)->orderBy('sort_order');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(PropertyTransfer::class);
    }
}
