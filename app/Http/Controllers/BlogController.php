<?php

namespace App\Http\Controllers;

use App\Models\BlogOffer;
use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('blog.index', ['posts' => Post::published()->latest('published_at')->paginate(12)]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->published, 404);

        $post->loadMissing('blogOffer');

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

        return view('blog.show', compact('post', 'offer'));
    }
}
