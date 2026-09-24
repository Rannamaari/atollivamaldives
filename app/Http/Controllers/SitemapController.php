<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Island;
use App\Models\LiveaboardPage;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $languageAlternates = static fn (string $englishUrl, string $arabicUrl): array => [
            'en' => $englishUrl,
            'ar' => $arabicUrl,
            'x-default' => $englishUrl,
        ];

        $arabicCoreUrls = collect([
            ['loc' => route('arabic.home'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '1.0', 'alternates' => $languageAlternates(route('home'), route('arabic.home'))],
            ['loc' => route('arabic.about'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.6', 'alternates' => $languageAlternates(route('about'), route('arabic.about'))],
            ['loc' => route('arabic.faq'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.6', 'alternates' => $languageAlternates(route('faq'), route('arabic.faq'))],
            ['loc' => route('arabic.blog.index'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7', 'alternates' => $languageAlternates(route('blog.index'), route('arabic.blog.index'))],
            ['loc' => route('arabic.resorts.index'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.9', 'alternates' => $languageAlternates(route('resorts.index'), route('arabic.resorts.index'))],
            ['loc' => route('arabic.guesthouses.index'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.9', 'alternates' => $languageAlternates(route('guesthouses.index'), route('arabic.guesthouses.index'))],
            ['loc' => route('arabic.cityhotels.index'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7', 'alternates' => $languageAlternates(route('cityhotels.index'), route('arabic.cityhotels.index'))],
            ['loc' => route('arabic.packages.index'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.8', 'alternates' => $languageAlternates(route('packages.index'), route('arabic.packages.index'))],
        ]);

        $arabicLiveaboardPage = LiveaboardPage::query()->first();

        if ($arabicLiveaboardPage?->hasArabicTranslation()) {
            $arabicCoreUrls->push([
                'loc' => route('arabic.liveaboards.index'),
                'lastmod' => optional($arabicLiveaboardPage->updated_at)->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => $languageAlternates(route('liveaboards.index'), route('arabic.liveaboards.index')),
            ]);
        }

        $coreAlternates = [
            'home' => $languageAlternates(route('home'), route('arabic.home')),
            'about' => $languageAlternates(route('about'), route('arabic.about')),
            'faq' => $languageAlternates(route('faq'), route('arabic.faq')),
            'blog' => $languageAlternates(route('blog.index'), route('arabic.blog.index')),
            'resorts' => $languageAlternates(route('resorts.index'), route('arabic.resorts.index')),
            'guesthouses' => $languageAlternates(route('guesthouses.index'), route('arabic.guesthouses.index')),
            'cityhotels' => $languageAlternates(route('cityhotels.index'), route('arabic.cityhotels.index')),
            'packages' => $languageAlternates(route('packages.index'), route('arabic.packages.index')),
            'liveaboards' => $arabicLiveaboardPage?->hasArabicTranslation()
                ? $languageAlternates(route('liveaboards.index'), route('arabic.liveaboards.index'))
                : [],
        ];

        $urls = collect([
            [
                'loc' => route('home'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '1.0',
                'alternates' => $coreAlternates['home'],
            ],
            [
                'loc' => route('about'),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'alternates' => $coreAlternates['about'],
            ],
            [
                'loc' => route('faq'),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'alternates' => $coreAlternates['faq'],
            ],
            [
                'loc' => route('partners.index'),
                'lastmod' => null,
                'changefreq' => 'monthly',
                'priority' => '0.7',
                'alternates' => [],
            ],
            [
                'loc' => route('blog.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.7',
                'alternates' => $coreAlternates['blog'],
            ],
            [
                'loc' => route('resorts.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.9',
                'alternates' => $coreAlternates['resorts'],
            ],
            [
                'loc' => route('guesthouses.index'),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.9',
                'alternates' => $coreAlternates['guesthouses'],
            ],
            [
                'loc' => route('cityhotels.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.7',
                'alternates' => $coreAlternates['cityhotels'],
            ],
            [
                'loc' => route('liveaboards.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => $coreAlternates['liveaboards'],
            ],
            [
                'loc' => route('packages.index'),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => $coreAlternates['packages'],
            ],
        ])->merge($arabicCoreUrls)->merge(
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
                ->map(function (Accommodation $accommodation) use ($languageAlternates): array {
                    $englishUrl = $accommodation->publicUrl();
                    $arabicUrl = url($accommodation->arabicPublicPath());

                    return [
                        'loc' => $englishUrl,
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
                        'alternates' => $accommodation->hasArabicTranslation()
                            ? $languageAlternates($englishUrl, $arabicUrl)
                            : [],
                        'image' => filled($accommodation->featured_image) || filled($accommodation->images)
                            ? $accommodation->seoImageUrl()
                            : null,
                        'image_title' => $accommodation->name,
                    ];
                })
        )->merge(
            Accommodation::published()
                ->with(['atollRelation', 'islandRelation'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->filter(fn (Accommodation $accommodation) => $accommodation->hasArabicTranslation())
                ->map(function (Accommodation $accommodation) use ($languageAlternates): array {
                    $englishUrl = $accommodation->publicUrl();
                    $arabicUrl = url($accommodation->arabicPublicPath());

                    return [
                        'loc' => $arabicUrl,
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
                        'alternates' => $languageAlternates($englishUrl, $arabicUrl),
                        'image' => filled($accommodation->featured_image) || filled($accommodation->images)
                            ? $accommodation->seoImageUrl()
                            : null,
                        'image_title' => $accommodation->arabic_name ?: $accommodation->name,
                    ];
                })
        )->merge(
            Post::published()
                ->orderByDesc('published_at')
                ->get()
                ->map(function (Post $post) use ($languageAlternates): array {
                    $englishUrl = url($post->publicPathForSlug());
                    $arabicUrl = url($post->arabicPublicPath());

                    return [
                        'loc' => $englishUrl,
                        'lastmod' => optional($post->updated_at)->toDateString(),
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                        'alternates' => $post->hasArabicTranslation()
                            ? $languageAlternates($englishUrl, $arabicUrl)
                            : [],
                        'image' => filled($post->featured_image) ? $post->seoImageUrl() : null,
                        'image_title' => $post->title,
                    ];
                })
        )->merge(
            Post::published()
                ->whereNotNull('arabic_title')
                ->whereNotNull('arabic_body')
                ->orderByDesc('published_at')
                ->get()
                ->map(function (Post $post) use ($languageAlternates): array {
                    $englishUrl = url($post->publicPathForSlug());
                    $arabicUrl = url($post->arabicPublicPath());

                    return [
                        'loc' => $arabicUrl,
                        'lastmod' => optional($post->updated_at)->toDateString(),
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                        'alternates' => $languageAlternates($englishUrl, $arabicUrl),
                        'image' => filled($post->featured_image) ? $post->seoImageUrl() : null,
                        'image_title' => $post->arabic_title ?: $post->title,
                    ];
                })
        )->unique('loc')->values();

        return response()
            ->view('seo.sitemap', ['urls' => $urls], 200, [
                'Content-Type' => 'application/xml; charset=UTF-8',
            ]);
    }
}
