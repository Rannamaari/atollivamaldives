<?php

namespace App\Http\Controllers;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\HomePage;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $hero = HomePage::active()->inRandomOrder()->first()
            ?? new HomePage([
                'kicker' => 'YOUR MALDIVES, THOUGHTFULLY PLANNED',
                'heading_line_one' => 'Find your way',
                'heading_line_two' => 'to',
                'heading_emphasis' => 'paradise.',
                'description' => 'Handpicked resorts, remarkable ocean journeys, and thoughtfully chosen Maldives travel products all in one place.',
                'explore_kicker' => 'EXPLORE MALDIVES',
                'explore_heading_line_one' => 'Browse our',
                'explore_heading_emphasis' => 'travel products.',
                'resorts_card_copy' => 'Private island escapes, overwater villas, and handpicked luxury stays.',
                'guesthouses_card_copy' => 'Local island stays for travellers seeking culture, value, and beach life.',
                'city_hotels_card_copy' => 'Convenient Malé and airport-area stays for stopovers and short visits.',
                'liveaboards_card_copy' => 'Ocean journeys designed around diving, surfing, and private charters.',
            ]);

        $resortCount = Accommodation::query()->where('type', AccommodationType::Resort->value)->where('status', '!=', 'inactive')->count();
        $guesthouseCount = Accommodation::query()->where('type', AccommodationType::Guesthouse->value)->where('status', '!=', 'inactive')->count();
        $cityHotelCount = Accommodation::query()->where('type', AccommodationType::CityHotel->value)->where('status', '!=', 'inactive')->count();
        $liveaboardCount = Accommodation::query()->where('type', AccommodationType::Liveaboard->value)->where('status', '!=', 'inactive')->count();

        return view('home', [
            'featuredProducts' => Accommodation::published()->where('featured', true)->orderBy('sort_order')->take(6)->get(),
            'posts' => Post::published()->latest('published_at')->take(3)->get(),
            'hero' => $hero,
            'exploreCards' => [
                [
                    'href' => route('resorts.index'),
                    'count' => $resortCount,
                    'label' => 'Resorts',
                    'description' => $hero->resorts_card_copy ?: 'Private island escapes, overwater villas, and handpicked luxury stays.',
                    'image' => $hero->resorts_card_image_url ?: $this->fallbackCategoryImage(AccommodationType::Resort),
                ],
                [
                    'href' => route('guesthouses.index'),
                    'count' => $guesthouseCount,
                    'label' => 'Guesthouses',
                    'description' => $hero->guesthouses_card_copy ?: 'Local island stays for travellers seeking culture, value, and beach life.',
                    'image' => $hero->guesthouses_card_image_url ?: $this->fallbackCategoryImage(AccommodationType::Guesthouse),
                ],
                [
                    'href' => route('cityhotels.index'),
                    'count' => $cityHotelCount,
                    'label' => 'City Hotels',
                    'description' => $hero->city_hotels_card_copy ?: 'Convenient Malé and airport-area stays for stopovers and short visits.',
                    'image' => $hero->city_hotels_card_image_url ?: $this->fallbackCategoryImage(AccommodationType::CityHotel),
                ],
                [
                    'href' => route('liveaboards.index'),
                    'count' => $liveaboardCount,
                    'label' => 'Liveaboards',
                    'description' => $hero->liveaboards_card_copy ?: 'Ocean journeys designed around diving, surfing, and private charters.',
                    'image' => $hero->liveaboards_card_image_url ?: $this->fallbackCategoryImage(AccommodationType::Liveaboard),
                ],
            ],
            'productCounts' => [
                'resorts' => $resortCount,
                'guesthouses' => $guesthouseCount,
                'cityHotels' => $cityHotelCount,
                'liveaboards' => $liveaboardCount,
            ],
        ]);
    }

    private function fallbackCategoryImage(AccommodationType $type): string
    {
        $product = Accommodation::query()
            ->where('type', $type->value)
            ->where('status', '!=', 'inactive')
            ->where(function ($query): void {
                $query->whereNotNull('featured_image')
                    ->orWhereJsonLength('images', '>', 0);
            })
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->first();

        $coverImage = $product?->cover_image;

        if ($coverImage && (str_starts_with($coverImage, 'http://') || str_starts_with($coverImage, 'https://'))) {
            return $coverImage;
        }

        return $coverImage
            ? asset('storage/'.ltrim($coverImage, '/'))
            : asset('storage/'.match ($type) {
                AccommodationType::Guesthouse => 'placeholders/guesthouse-placeholder.svg',
                AccommodationType::CityHotel => 'placeholders/city-hotel-placeholder.svg',
                AccommodationType::Liveaboard => 'placeholders/liveaboard-placeholder.svg',
                default => 'placeholders/resort-placeholder.svg',
            });
    }
}
