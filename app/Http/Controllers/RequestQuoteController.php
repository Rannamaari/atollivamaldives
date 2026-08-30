<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoManager;
use Illuminate\View\View;

class RequestQuoteController extends Controller
{
    public function __invoke(SeoManager $seoManager): View
    {
        return view('inquiries.request-quote', [
            'seo' => array_merge($seoManager->forSimplePage(
                title: 'Request a Maldives Quote | Atolliva Maldives',
                description: 'Tell Atolliva Maldives your dates, budget, travel style and preferences, and we will help shortlist the right Maldives stays for you.',
                canonical: route('request-quote'),
                breadcrumbs: [
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Request Quote', 'url' => route('request-quote')],
                ],
            )->toArray(), [
                'robots' => 'noindex, follow',
            ]),
        ]);
    }
}
