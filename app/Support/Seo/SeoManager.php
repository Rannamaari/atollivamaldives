<?php

namespace App\Support\Seo;

use App\Models\Accommodation;
use App\Models\LiveaboardPage;
use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SeoManager
{
    public function __construct(
        protected SiteSetting $siteSetting,
    ) {
    }

    public function defaults(): SeoData
    {
        $settings = $this->siteSetting->current();
        $siteName = $settings->site_name ?: 'Atolliva Maldives';
        $title = $settings->default_meta_title ?: 'Maldives Travel Agency | Resorts, Guesthouses & Holiday Packages | Atolliva Maldives';
        $description = $settings->default_meta_description ?: 'Discover handpicked Maldives resorts, guesthouses, liveaboards and personalised holiday packages with local travel experts at Atolliva Maldives.';
        $canonical = url()->current();
        $image = $settings->default_og_image_url ?: asset('logo/optimized/atolliva-share.png');

        return new SeoData(
            siteName: $siteName,
            title: $title,
            description: $description,
            canonical: $canonical,
            ogTitle: $title,
            ogDescription: $description,
            ogImage: $image,
            twitterCard: 'summary_large_image',
            robots: $this->robotsValue(
                (bool) ($settings->default_robots_index ?? true),
                (bool) ($settings->default_robots_follow ?? true),
            ),
            searchConsoleVerification: $settings->google_search_console_verification ?: config('services.analytics.search_console_verification'),
            breadcrumbs: [
                ['name' => 'Home', 'url' => route('home')],
            ],
            schema: $this->defaultSchema($settings, $canonical, $siteName),
        );
    }

    public function forCurrentRequest(array $overrides = []): SeoData
    {
        $defaults = $this->defaults()->toArray();
        $merged = array_merge($defaults, array_filter($overrides, fn ($value) => $value !== null && $value !== ''));

        $schema = array_values(array_filter([
            ...Arr::wrap($defaults['schema'] ?? []),
            ...Arr::wrap($overrides['schema'] ?? []),
        ]));

        return new SeoData(
            siteName: (string) ($merged['site_name'] ?? $defaults['site_name']),
            title: (string) ($merged['title'] ?? $defaults['title']),
            description: (string) ($merged['description'] ?? $defaults['description']),
            canonical: (string) ($merged['canonical'] ?? $defaults['canonical']),
            ogTitle: (string) ($merged['og_title'] ?? $merged['title'] ?? $defaults['title']),
            ogDescription: (string) ($merged['og_description'] ?? $merged['description'] ?? $defaults['description']),
            ogImage: (string) ($merged['og_image'] ?? $defaults['og_image']),
            twitterCard: (string) ($merged['twitter_card'] ?? 'summary_large_image'),
            robots: (string) ($merged['robots'] ?? $defaults['robots']),
            searchConsoleVerification: $merged['search_console_verification'] ?? $defaults['search_console_verification'],
            breadcrumbs: $merged['breadcrumbs'] ?? $defaults['breadcrumbs'] ?? [],
            schema: $schema,
        );
    }

    public function forAccommodation(Accommodation $accommodation): SeoData
    {
        $title = $accommodation->seo_title ?: $accommodation->seoTitleFallback();
        $description = $accommodation->seoDescriptionFallback();
        $breadcrumbs = $accommodation->seoBreadcrumbs();

        return $this->forCurrentRequest([
            'title' => $title,
            'description' => $description,
            'canonical' => $accommodation->publicUrl(),
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $accommodation->seoImageUrl(),
            'breadcrumbs' => $breadcrumbs,
            'schema' => [
                $this->breadcrumbSchema($breadcrumbs),
                $this->accommodationSchema($accommodation),
            ],
        ]);
    }

    public function forPost(Post $post): SeoData
    {
        $title = $post->seo_title ?: $post->seoTitleFallback();
        $description = $post->seoDescriptionFallback();
        $breadcrumbs = $post->seoBreadcrumbs();

        return $this->forCurrentRequest([
            'title' => $title,
            'description' => $description,
            'canonical' => url($post->publicPathForSlug()),
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $post->seoImageUrl(),
            'breadcrumbs' => $breadcrumbs,
            'schema' => [
                $this->breadcrumbSchema($breadcrumbs),
                $this->articleSchema($post),
            ],
        ]);
    }

    public function forListing(string $title, string $description, string $canonical, array $breadcrumbs, ?string $image = null): SeoData
    {
        return $this->forCurrentRequest([
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $image,
            'breadcrumbs' => $breadcrumbs,
            'schema' => [
                $this->breadcrumbSchema($breadcrumbs),
            ],
        ]);
    }

    public function forSimplePage(string $title, string $description, string $canonical, array $breadcrumbs, ?string $image = null, array $extraSchema = []): SeoData
    {
        return $this->forCurrentRequest([
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $image,
            'breadcrumbs' => $breadcrumbs,
            'schema' => [
                $this->breadcrumbSchema($breadcrumbs),
                ...$extraSchema,
            ],
        ]);
    }

    public function forLiveaboardLanding(LiveaboardPage $page): SeoData
    {
        $title = 'Maldives Liveaboards | Diving Cruises & Private Charters | Atolliva Maldives';
        $description = trim(strip_tags($page->intro ?: 'Discover liveaboard charters and voyages across the Maldives with Atolliva Maldives.'));

        return $this->forSimplePage(
            title: $title,
            description: $description,
            canonical: route('liveaboards.index'),
            breadcrumbs: [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Liveaboards', 'url' => route('liveaboards.index')],
            ],
            image: $page->hero_image_url,
        );
    }

    protected function defaultSchema(SiteSetting $settings, string $canonical, string $siteName): array
    {
        $organization = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => $siteName,
            'url' => url('/'),
            'description' => $settings->company_description ?: $settings->default_meta_description,
            'logo' => $settings->business_logo_url ?: asset('logo/optimized/atolliva-share.png'),
            'email' => $settings->business_email ?: 'hello@atollivamaldives.com',
            'telephone' => $settings->business_phone ?: '+960 9996210',
            'address' => filled($settings->business_address) ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings->business_address,
                'addressCountry' => 'MV',
            ] : null,
            'sameAs' => array_values(array_filter([
                $settings->facebook_url,
                $settings->instagram_url,
                $settings->x_url,
                $settings->tiktok_url,
            ])),
        ], fn ($value) => filled($value) || is_array($value));

        $website = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'description' => $settings->default_meta_description ?: $settings->company_description,
            'inLanguage' => app()->getLocale(),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/resorts').'?destination={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
            'mainEntityOfPage' => $canonical,
        ];

        return [$organization, $website];
    }

    protected function breadcrumbSchema(array $breadcrumbs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbs)->values()->map(fn (array $item, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    protected function articleSchema(Post $post): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->seoDescriptionFallback(),
            'image' => [$post->seoImageUrl()],
            'datePublished' => optional($post->published_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => $post->author ?: 'Atolliva Maldives',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $this->siteSetting->current()->site_name ?: 'Atolliva Maldives',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->siteSetting->current()->business_logo_url,
                ],
            ],
            'mainEntityOfPage' => url($post->publicPathForSlug()),
            'articleSection' => $post->category,
        ], fn ($value) => filled($value) || is_array($value));
    }

    protected function accommodationSchema(Accommodation $accommodation): array
    {
        $type = match ($accommodation->type->value) {
            'resort', 'guesthouse', 'city_hotel' => 'LodgingBusiness',
            'liveaboard' => 'TouristTrip',
            'package' => 'Product',
            default => 'Thing',
        };

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $accommodation->name,
            'description' => $accommodation->seoDescriptionFallback(),
            'image' => [$accommodation->seoImageUrl()],
            'url' => $accommodation->publicUrl(),
            'address' => filled($accommodation->address) ? [
                '@type' => 'PostalAddress',
                'addressLocality' => $accommodation->islandRelation?->name ?: $accommodation->island,
                'addressRegion' => $accommodation->atollRelation?->name ?: $accommodation->atoll,
                'streetAddress' => Str::limit(strip_tags((string) $accommodation->address), 140),
                'addressCountry' => $accommodation->country ?: 'Maldives',
            ] : null,
            'offers' => $accommodation->price_from ? [
                '@type' => 'Offer',
                'priceCurrency' => $accommodation->currency ?: 'USD',
                'price' => (float) $accommodation->price_from,
                'url' => $accommodation->publicUrl(),
                'availability' => 'https://schema.org/InStock',
            ] : null,
        ], fn ($value) => filled($value) || is_array($value));
    }

    protected function robotsValue(bool $index, bool $follow): string
    {
        return sprintf('%s, %s', $index ? 'index' : 'noindex', $follow ? 'follow' : 'nofollow');
    }
}
