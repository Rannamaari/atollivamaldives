<?php

namespace App\Http\Controllers;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Island;
use App\Models\Post;
use App\Services\SocialShareService;
use App\Support\Seo\SeoManager;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(Request $request, SeoManager $seoManager): View
    {
        $type = $request->route('type') ?: $request->string('type')->toString();
        $atoll = $request->route('atoll');
        $island = $request->route('island');
        $selectedType = collect(AccommodationType::cases())->first(fn (AccommodationType $case) => $case->value === $type);
        $destination = trim($request->string('destination')->toString());
        $checkIn = $request->string('check_in')->toString();
        $checkOut = $request->string('check_out')->toString();
        $adults = max(1, (int) $request->integer('adults', 2));
        $children = max(0, (int) $request->integer('children', 0));
        $hasSearchFilters = filled($destination) || filled($checkIn) || filled($checkOut) || $request->hasAny(['adults', 'children']);
        $shouldNoIndex = $request->routeIs('accommodations.index') || $hasSearchFilters;

        $items = Accommodation::published()
            ->with(['facilities', 'transfers', 'atollRelation', 'islandRelation'])
            ->withCount(['rooms as published_rooms_count' => fn ($query) => $query->where('status', 'published')])
            ->when($selectedType, fn ($query) => $query->where('type', $selectedType->value))
            ->when($atoll instanceof Atoll, fn ($query) => $query->where('atoll_id', $atoll->getKey()))
            ->when($island instanceof Island, fn ($query) => $query->where('island_id', $island->getKey()))
            ->when($destination, function ($query, $destination) {
                $query->where(function ($nested) use ($destination) {
                    $nested
                        ->where('name', 'like', '%'.$destination.'%')
                        ->orWhere('island', 'like', '%'.$destination.'%')
                        ->orWhere('atoll', 'like', '%'.$destination.'%')
                        ->orWhere('city', 'like', '%'.$destination.'%')
                        ->orWhereHas('islandRelation', fn ($island) => $island->where('name', 'like', '%'.$destination.'%'))
                        ->orWhereHas('atollRelation', fn ($atoll) => $atoll->where('name', 'like', '%'.$destination.'%'));
                });
            })
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        return view('accommodations.index', [
            'items' => $items,
            'selectedType' => $selectedType,
            'selectedAtoll' => $atoll instanceof Atoll ? $atoll : null,
            'selectedIsland' => $island instanceof Island ? $island : null,
            'seo' => array_merge($seoManager->forListing(
                title: $this->listingSeoTitle($selectedType, $atoll instanceof Atoll ? $atoll : null, $island instanceof Island ? $island : null),
                description: $this->listingSeoDescription($selectedType, $atoll instanceof Atoll ? $atoll : null, $island instanceof Island ? $island : null),
                canonical: $this->listingCanonicalUrl($selectedType, $atoll instanceof Atoll ? $atoll : null, $island instanceof Island ? $island : null),
                breadcrumbs: $this->listingBreadcrumbs($selectedType, $atoll instanceof Atoll ? $atoll : null, $island instanceof Island ? $island : null),
            )->toArray(), [
                'robots' => $shouldNoIndex ? 'noindex, follow' : null,
            ]),
            'searchState' => [
                'destination' => $destination,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'adults' => $adults,
                'children' => $children,
            ],
        ]);
    }

    public function show(Accommodation $accommodation): View
    {
        return $this->renderShow($accommodation);
    }

    public function resortShow(Accommodation $accommodation): View
    {
        abort_unless($accommodation->type === AccommodationType::Resort, 404);

        return $this->renderShow($accommodation);
    }

    public function cityHotelShow(Accommodation $accommodation): View
    {
        abort_unless($accommodation->type === AccommodationType::CityHotel, 404);

        return $this->renderShow($accommodation);
    }

    public function liveaboardShow(Accommodation $accommodation): View
    {
        abort_unless($accommodation->type === AccommodationType::Liveaboard, 404);

        return $this->renderShow($accommodation);
    }

    public function packageShow(string $category, Accommodation $accommodation): View|RedirectResponse
    {
        abort_unless($accommodation->type === AccommodationType::Package, 404);

        if ($category !== $accommodation->packageCategorySlug()) {
            return redirect()->to($accommodation->publicPathForSlug(), 301);
        }

        return $this->renderShow($accommodation);
    }

    public function guesthouseShow(Atoll $atoll, Island $island, Accommodation $accommodation): View|RedirectResponse
    {
        abort_unless($accommodation->type === AccommodationType::Guesthouse, 404);
        abort_unless($accommodation->atoll_id === $atoll->getKey() && $accommodation->island_id === $island->getKey(), 404);

        $canonicalPath = $accommodation->publicPathForSlug();

        if (request()->path() !== ltrim($canonicalPath, '/')) {
            return redirect()->to($canonicalPath, 301);
        }

        return $this->renderShow($accommodation);
    }

    public function legacyShow(Accommodation $accommodation): RedirectResponse
    {
        abort_unless($accommodation->published, 404);

        $query = request()->getQueryString();

        return redirect()->to(
            $accommodation->publicPathForSlug().($query ? '?'.$query : ''),
            301
        );
    }

    protected function renderShow(Accommodation $accommodation): View
    {
        abort_unless($accommodation->published, 404);

        $accommodation->load([
            'facilities',
            'transfers',
            'galleryImages',
            'atollRelation',
            'islandRelation',
            'rooms' => fn ($query) => $query
                ->where('status', 'published')
                ->with(['facilities', 'images'])
                ->orderBy('sort_order'),
        ]);

        $similarProperties = Accommodation::published()
            ->with(['facilities'])
            ->whereKeyNot($accommodation->getKey())
            ->where(function ($query) use ($accommodation) {
                $query
                    ->where('type', $accommodation->type->value)
                    ->orWhere('atoll', $accommodation->atoll);
            })
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $relatedPosts = Post::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('accommodations.show', [
            'accommodation' => $accommodation,
            'similarProperties' => $similarProperties,
            'relatedPosts' => $relatedPosts,
            'seo' => app(SeoManager::class)->forAccommodation($accommodation)->toArray(),
            'socialShare' => app(SocialShareService::class)->for($accommodation)->toArray(),
        ]);
    }

    protected function listingSeoTitle(?AccommodationType $selectedType, ?Atoll $atoll, ?Island $island): string
    {
        if ($island && $atoll) {
            return $island->name.' Guesthouses & Maldives Holiday Packages | Atolliva Maldives';
        }

        if ($atoll) {
            return $atoll->name.' Guesthouses & Maldives Island Stays | Atolliva Maldives';
        }

        return match ($selectedType?->value) {
            'resort' => 'Maldives Resorts | Rates & Holiday Packages | Atolliva Maldives',
            'guesthouse' => 'Maldives Guesthouses & Local Island Holidays | Atolliva Maldives',
            'liveaboard' => 'Maldives Liveaboards | Diving Cruises & Charters | Atolliva Maldives',
            'city_hotel' => 'Maldives City Hotels | Malé & Airport Area Stays | Atolliva Maldives',
            'package' => 'Maldives Holiday Packages | Atolliva Maldives',
            default => 'Maldives Travel Products | Resorts, Guesthouses & Packages | Atolliva Maldives',
        };
    }

    protected function listingSeoDescription(?AccommodationType $selectedType, ?Atoll $atoll, ?Island $island): string
    {
        if ($island && $atoll) {
            return "Explore guesthouses in {$island->name}, {$atoll->name}, with room options, island stay details, transfers and Maldives holiday planning from Atolliva Maldives.";
        }

        if ($atoll) {
            return "Explore guesthouses across {$atoll->name} with island stay details, transfer guidance and Maldives holiday planning from Atolliva Maldives.";
        }

        return match ($selectedType?->value) {
            'resort' => 'Explore handpicked Maldives resorts with rate guidance, villa options, meal plans and holiday planning support from Atolliva Maldives.',
            'guesthouse' => 'Explore Maldives guesthouses and local island holidays with room options, transfer guidance and planning support from Atolliva Maldives.',
            'liveaboard' => 'Explore Maldives liveaboards, diving cruises and private charters with route guidance and planning support from Atolliva Maldives.',
            'city_hotel' => 'Explore city hotels in Malé and airport-area stays with transfer guidance and stopover planning support from Atolliva Maldives.',
            'package' => 'Explore Maldives holiday packages with accommodation, transfers and thoughtful planning from Atolliva Maldives.',
            default => 'Explore resorts, guesthouses, city hotels, packages and liveaboards across the Maldives with Atolliva Maldives.',
        };
    }

    protected function listingCanonicalUrl(?AccommodationType $selectedType, ?Atoll $atoll, ?Island $island): string
    {
        if ($island && $atoll) {
            return route('guesthouses.island', [$atoll, $island]);
        }

        if ($atoll) {
            return route('guesthouses.atoll', $atoll);
        }

        return match ($selectedType?->value) {
            'resort' => route('resorts.index'),
            'guesthouse' => route('guesthouses.index'),
            'liveaboard' => route('liveaboards.index'),
            'city_hotel' => route('cityhotels.index'),
            'package' => route('packages.index'),
            default => route('accommodations.index'),
        };
    }

    protected function listingBreadcrumbs(?AccommodationType $selectedType, ?Atoll $atoll, ?Island $island): array
    {
        $breadcrumbs = [['name' => 'Home', 'url' => route('home')]];

        if ($selectedType?->value === 'guesthouse') {
            $breadcrumbs[] = ['name' => 'Guesthouses', 'url' => route('guesthouses.index')];

            if ($atoll) {
                $breadcrumbs[] = ['name' => $atoll->name, 'url' => route('guesthouses.atoll', $atoll)];
            }

            if ($atoll && $island) {
                $breadcrumbs[] = ['name' => $island->name, 'url' => route('guesthouses.island', [$atoll, $island])];
            }

            return $breadcrumbs;
        }

        if ($selectedType) {
            $route = match ($selectedType->value) {
                'resort' => route('resorts.index'),
                'liveaboard' => route('liveaboards.index'),
                'city_hotel' => route('cityhotels.index'),
                'package' => route('packages.index'),
                default => route('accommodations.index'),
            };

            $breadcrumbs[] = ['name' => $selectedType->label(), 'url' => $route];

            return $breadcrumbs;
        }

        $breadcrumbs[] = ['name' => 'Travel Products', 'url' => route('accommodations.index')];

        return $breadcrumbs;
    }
}
