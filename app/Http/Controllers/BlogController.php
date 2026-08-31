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
        return view('blog.index', [
            'posts' => Post::published()->latest('published_at')->paginate(12),
            'seo' => $seoManager->forListing(
                title: 'Maldives Travel Blog & Guides | Atolliva Maldives',
                description: 'Read Maldives travel guides, resort advice, local island ideas, liveaboard inspiration and holiday planning tips from Atolliva Maldives.',
                canonical: route('blog.index'),
                breadcrumbs: [
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                ],
            )->toArray(),
        ]);
    }

    public function show(Post $post, SeoManager $seoManager, SocialShareService $socialShareService): View
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
            'seo' => $seoManager->forPost($post)->toArray(),
            'socialShare' => $socialShareService->for($post)->toArray(),
        ]);
    }
}
