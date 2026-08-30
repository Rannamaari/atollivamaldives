<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoManager;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(SeoManager $seoManager): View
    {
        return view('about', [
            'officeAddress' => 'M. Ithaamuiyge 1, Aliasmagu, Maldives',
            'seo' => $seoManager->forSimplePage(
                title: 'About Atolliva Maldives | Your Maldives, Thoughtfully Planned',
                description: 'Learn the story behind Atolliva Maldives, our Maldives-based approach to travel planning, and where our office is located.',
                canonical: route('about'),
                breadcrumbs: [
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'About Us', 'url' => route('about')],
                ],
            )->toArray(),
        ]);
    }
}
