<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\LiveaboardPage;
use App\Support\Seo\SeoManager;
use Illuminate\View\View;

class LiveaboardController extends Controller
{
    public function __invoke(SeoManager $seoManager): View
    {
        $isArabic = app()->getLocale() === 'ar';
        $page = LiveaboardPage::current();

        $liveaboards = Accommodation::published()
            ->where('type', 'liveaboard')
            ->orderBy('sort_order')
            ->get();

        return view('liveaboards.index', [
            'page' => $page,
            'liveaboards' => $liveaboards,
            'seo' => $seoManager->forLiveaboardLanding($page)->toArray(),
            'isArabic' => $isArabic,
            'alternateLanguages' => $isArabic
                ? ['en' => route('liveaboards.index'), 'x-default' => route('liveaboards.index')]
                : ['ar' => route('arabic.liveaboards.index'), 'x-default' => route('liveaboards.index')],
        ]);
    }
}
