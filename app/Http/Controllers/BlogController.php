<?php

namespace App\Http\Controllers;

use App\Models\BlogOffer;
use App\Models\Post;
use App\Services\SocialShareService;
use App\Support\Seo\SeoManager;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(SeoManager $seoManager): View
    {
        $isArabic = app()->getLocale() === 'ar';
        $blogRoute = $isArabic ? 'arabic.blog.index' : 'blog.index';

        return view('blog.index', [
            'posts' => Post::published()->latest('published_at')->paginate(12),
            'isArabic' => $isArabic,
            'seo' => $seoManager->forListing(
                title: $isArabic ? 'مدونة المالديف ودليل السفر | أتوليفا المالديف' : 'Maldives Travel Blog & Guides | Atolliva Maldives',
                description: $isArabic ? 'اقرأ أدلة السفر إلى المالديف وأفكار الجزر ونصائح المنتجعات ورحلات القوارب من أتوليفا المالديف.' : 'Read Maldives travel guides, resort advice, local island ideas, liveaboard inspiration and holiday planning tips from Atolliva Maldives.',
                canonical: route($blogRoute),
                breadcrumbs: [
                    ['name' => $isArabic ? 'الرئيسية' : 'Home', 'url' => route($isArabic ? 'arabic.home' : 'home')],
                    ['name' => $isArabic ? 'المدونة' : 'Blog', 'url' => route($blogRoute)],
                ],
            )->toArray(),
            'alternateLanguages' => $isArabic
                ? ['en' => route('blog.index'), 'x-default' => route('blog.index')]
                : ['ar' => route('arabic.blog.index'), 'x-default' => route('blog.index')],
        ]);
    }

    public function show(Post $post, SeoManager $seoManager, SocialShareService $socialShareService): View
    {
        return $this->showPost($post, $seoManager, $socialShareService);
    }

    public function arabicShow(Post $post, SeoManager $seoManager, SocialShareService $socialShareService): View
    {
        abort_unless($post->hasArabicTranslation(), 404);
        app()->setLocale('ar');

        return $this->showPost($post, $seoManager, $socialShareService, true);
    }

    protected function showPost(Post $post, SeoManager $seoManager, SocialShareService $socialShareService, bool $arabic = false): View
    {
        abort_unless($post->published, 404);

        $post->loadMissing('blogOffer');
        $relatedPosts = Post::published()
            ->whereKeyNot($post->getKey())
            ->when(filled($post->category), fn ($query) => $query->where('category', $post->category))
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($relatedPosts->count() < 3) {
            $relatedPosts = $relatedPosts->concat(
                Post::published()
                    ->whereKeyNot($post->getKey())
                    ->whereNotIn('id', $relatedPosts->pluck('id'))
                    ->latest('published_at')
                    ->take(3 - $relatedPosts->count())
                    ->get()
            );
        }

        $specifiedOffer = $post->blogOffer;

        if ($specifiedOffer?->active) {
            $offer = $specifiedOffer;
        } else {
            $offers = BlogOffer::active()->orderBy('sort_order')->get();

            $categoryOffers = $offers
                ->filter(fn (BlogOffer $offer) => filled($post->category) && in_array($post->category, $offer->target_categories ?? [], true))
                ->values();

            $generalOffers = $offers
                ->filter(fn (BlogOffer $offer) => empty($offer->target_categories))
                ->values();

            $pool = $categoryOffers->isNotEmpty() ? $categoryOffers : $generalOffers;
            $offer = $pool->isEmpty() ? null : $pool[($post->id - 1) % $pool->count()];
        }

        return view('blog.show', [
            'post' => $post,
            'offer' => $offer,
            'relatedPosts' => $relatedPosts,
            'isArabic' => $arabic,
            'seo' => ($arabic ? $seoManager->forArabicPost($post) : $seoManager->forPost($post))->toArray(),
            'socialShare' => $arabic ? null : $socialShareService->for($post)->toArray(),
            'documentLocale' => $arabic ? 'ar' : 'en',
            'alternateLanguages' => $arabic
                ? ['en' => route('blog.show', $post), 'x-default' => route('blog.show', $post)]
                : ($post->hasArabicTranslation() ? ['ar' => route('blog.arabic.show', $post), 'x-default' => route('blog.show', $post)] : []),
        ]);
    }
}
