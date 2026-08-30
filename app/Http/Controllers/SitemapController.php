<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Island;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            [
                'loc' => route('home'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('about'),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('faq'),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('blog.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('resorts.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('guesthouses.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('cityhotels.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('liveaboards.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('packages.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
        ])->merge(
            Atoll::query()
                ->where('status', 'published')
                ->orderBy('name')
                ->get()
                ->map(fn (Atoll $atoll) => [
                    'loc' => route('guesthouses.atoll', $atoll),
                    'lastmod' => optional($atoll->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ])
        )->merge(
            Island::query()
                ->where('status', 'published')
                ->whereHas('atoll', fn ($query) => $query->where('status', 'published'))
                ->with('atoll')
                ->orderBy('name')
                ->get()
                ->map(fn (Island $island) => [
                    'loc' => route('guesthouses.island', [$island->atoll, $island]),
                    'lastmod' => optional($island->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ])
        )->merge(
            Accommodation::published()
                ->with(['atollRelation', 'islandRelation'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(fn (Accommodation $accommodation) => [
                    'loc' => $accommodation->publicUrl(),
                    'lastmod' => optional($accommodation->updated_at)->toDateString(),
                    'changefreq' => match ($accommodation->type->value) {
                        'resort', 'guesthouse' => 'weekly',
                        'liveaboard', 'package', 'city_hotel' => 'monthly',
                        default => 'monthly',
                    },
                    'priority' => match ($accommodation->type->value) {
                        'resort' => '0.8',
                        'guesthouse' => '0.8',
                        'liveaboard' => '0.7',
                        'package' => '0.7',
                        'city_hotel' => '0.6',
                        default => '0.5',
                    },
                ])
        )->merge(
            Post::published()
                ->orderByDesc('published_at')
                ->get()
                ->map(fn (Post $post) => [
                    'loc' => url($post->publicPathForSlug()),
                    'lastmod' => optional($post->updated_at)->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ])
        )->unique('loc')->values();

        return response()
            ->view('seo.sitemap', ['urls' => $urls], 200, [
                'Content-Type' => 'application/xml; charset=UTF-8',
            ]);
    }
}
