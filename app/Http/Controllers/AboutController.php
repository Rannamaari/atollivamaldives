<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Support\Seo\SeoManager;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(SeoManager $seoManager): View
    {
        $isArabic = app()->getLocale() === 'ar';
        $settings = SiteSetting::current();
        $officeAddress = implode(', ', array_filter([
            $settings->business_address ?: 'M. Ithaamuiyge 1, Alimasmagu',
            $settings->business_address_locality ?: 'Male City',
            'Maldives',
        ]));

        return view('about', [
            'officeAddress' => $officeAddress,
            'isArabic' => $isArabic,
            'seo' => $seoManager->forSimplePage(
                title: $isArabic ? 'عن أتوليفا المالديف | عطلتك في المالديف مخططة بعناية' : 'About Atolliva Maldives | Your Maldives, Thoughtfully Planned',
                description: $isArabic ? 'تعرف على أتوليفا المالديف وخدماتنا في تخطيط العطلات الفاخرة والاقتصادية والملائمة لاحتياجات المسافرين المسلمين في المالديف.' : 'Learn the story behind Atolliva Maldives, our Maldives-based approach to travel planning, and where our office is located.',
                canonical: route($isArabic ? 'arabic.about' : 'about'),
                breadcrumbs: [
                    ['name' => $isArabic ? 'الرئيسية' : 'Home', 'url' => route($isArabic ? 'arabic.home' : 'home')],
                    ['name' => $isArabic ? 'من نحن' : 'About Us', 'url' => route($isArabic ? 'arabic.about' : 'about')],
                ],
            )->toArray(),
            'alternateLanguages' => $isArabic
                ? ['en' => route('about'), 'x-default' => route('about')]
                : ['ar' => route('arabic.about'), 'x-default' => route('about')],
        ]);
    }
}
