<?php

namespace App\Models;

use App\Enums\AccommodationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function publicPathForSlug(?string $slug = null): string
    {
        $slug ??= $this->slug;

        return match ($this->type) {
            AccommodationType::Resort => route('resorts.show', ['accommodation' => $slug], false),
            AccommodationType::Guesthouse => route('guesthouses.show', [
                'atoll' => $this->atollRelation?->slug ?? Str::slug((string) $this->atoll),
                'island' => $this->islandRelation?->slug ?? Str::slug((string) $this->island),
                'accommodation' => $slug,
            ], false),
            AccommodationType::Liveaboard => route('liveaboards.show', ['accommodation' => $slug], false),
            AccommodationType::CityHotel => route('cityhotels.show', ['accommodation' => $slug], false),
            AccommodationType::Package => route('packages.show', [
                'category' => $this->packageCategorySlug(),
                'accommodation' => $slug,
            ], false),
        };
    }

    public function publicUrl(array $query = []): string
    {
        $path = $this->publicPathForSlug($this->slug);

        if ($query === []) {
            return url($path);
        }

        return url($path).'?'.http_build_query($query);
    }

    public function packageCategorySlug(): string
    {
        return Str::slug((string) ($this->property_subtype ?: 'general'));
    }

    public function seoTitleFallback(): string
    {
        return match ($this->type) {
            AccommodationType::Resort => $this->name.' | Rates & Holiday Packages | Atolliva Maldives',
            AccommodationType::Guesthouse => collect([$this->name, $this->islandRelation?->name ?: $this->island])
                ->filter()
                ->implode(', ').' | Maldives Guesthouse | Atolliva Maldives',
            AccommodationType::Liveaboard => $this->name.' | Maldives Liveaboard Packages | Atolliva Maldives',
            AccommodationType::Package => $this->name.' | Maldives Holiday Package | Atolliva Maldives',
            AccommodationType::CityHotel => $this->name.' | Maldives City Hotel | Atolliva Maldives',
        };
    }

    public function seoDescriptionFallback(): string
    {
        if (filled($this->seo_description)) {
            return (string) $this->seo_description;
        }

        if (filled($this->summary)) {
            return trim(strip_tags((string) $this->summary));
        }

        $location = collect([$this->islandRelation?->name ?: $this->island, $this->atollRelation?->name ?: $this->atoll])
            ->filter()
            ->implode(', ');

        return match ($this->type) {
            AccommodationType::Resort => trim("Explore {$this->name} resort rates, villas, meal plans, transfers and Maldives holiday packages with Atolliva Maldives.".($location ? " Located in {$location}." : '')),
            AccommodationType::Guesthouse => trim("Discover {$this->name}".($location ? " in {$location}" : '')." with local island stay details, room options, transfers and Maldives guesthouse holiday planning from Atolliva Maldives."),
            AccommodationType::Liveaboard => trim("Explore {$this->name} liveaboard routes, cabins, diving options and Maldives cruise planning with Atolliva Maldives.".($location ? " Operating around {$location}." : '')),
            AccommodationType::Package => trim("Explore {$this->name} with accommodation, transfers and Maldives holiday planning support from Atolliva Maldives."),
            AccommodationType::CityHotel => trim("Explore {$this->name}".($location ? " in {$location}" : '')." with city stay details, transfer information and Maldives stopover planning from Atolliva Maldives."),
        };
    }

    public function seoImageUrl(): string
    {
        $image = $this->cover_image;

        return str_starts_with($image, 'http')
            ? $image
            : asset('storage/'.ltrim($image, '/'));
    }

    public function seoBreadcrumbs(): array
    {
        $home = [['name' => 'Home', 'url' => route('home')]];

        return match ($this->type) {
            AccommodationType::Resort => [
                ...$home,
                ['name' => 'Resorts', 'url' => route('resorts.index')],
                ['name' => $this->name, 'url' => url($this->publicPathForSlug())],
            ],
            AccommodationType::Guesthouse => array_values(array_filter([
                ...$home,
                ['name' => 'Guesthouses', 'url' => route('guesthouses.index')],
                $this->atollRelation ? ['name' => $this->atollRelation->name, 'url' => route('guesthouses.atoll', $this->atollRelation)] : null,
                $this->atollRelation && $this->islandRelation ? ['name' => $this->islandRelation->name, 'url' => route('guesthouses.island', [$this->atollRelation, $this->islandRelation])] : null,
                ['name' => $this->name, 'url' => url($this->publicPathForSlug())],
            ])),
            AccommodationType::Liveaboard => [
                ...$home,
                ['name' => 'Liveaboards', 'url' => route('liveaboards.index')],
                ['name' => $this->name, 'url' => url($this->publicPathForSlug())],
            ],
            AccommodationType::Package => [
                ...$home,
                ['name' => 'Packages', 'url' => route('packages.index')],
                ['name' => Str::headline($this->packageCategorySlug()), 'url' => route('packages.show', ['category' => $this->packageCategorySlug(), 'accommodation' => $this])],
                ['name' => $this->name, 'url' => url($this->publicPathForSlug())],
            ],
            AccommodationType::CityHotel => [
                ...$home,
                ['name' => 'City Hotels', 'url' => route('cityhotels.index')],
                ['name' => $this->name, 'url' => url($this->publicPathForSlug())],
            ],
        };
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
