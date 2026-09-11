@php
    $wa = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode($whatsAppText ?? 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.');
    $isHome = request()->routeIs('home');
    $isLiveaboards = request()->routeIs('liveaboards.*');
    $isBlog = request()->routeIs('blog.*');
    $isFaq = request()->routeIs('faq');
    $isAbout = request()->routeIs('about');
    $currentAccommodation = request()->route('accommodation');
    $currentType = $currentAccommodation?->type?->value ?? request('type');
    $isResorts = request()->routeIs('resorts.index') || (request()->routeIs('accommodations.*') && $currentType === 'resort');
    $isGuestHouses = request()->routeIs('guesthouses.index') || (request()->routeIs('accommodations.*') && $currentType === 'guesthouse');
    $isCityHotels = request()->routeIs('cityhotels.index') || (request()->routeIs('accommodations.*') && $currentType === 'city_hotel');
    $isPackages = request()->routeIs('packages.index') || (request()->routeIs('accommodations.*') && $currentType === 'package');
    $isArabic = app()->getLocale() === 'ar';
    $route = static fn (string $name, array $parameters = []) => route(($isArabic ? 'arabic.' : '').$name, $parameters);
@endphp

<header class="{{ $class ?? '' }}">
    @include('partials.logo', ['dark' => $dark ?? false])

    <nav data-nav>
        <a href="{{ $route('resorts.index') }}" @class(['is-active' => $isResorts])>{{ $isArabic ? 'المنتجعات' : 'Resorts' }}</a>
        <a href="{{ $route('guesthouses.index') }}" @class(['is-active' => $isGuestHouses])>{{ $isArabic ? 'بيوت الضيافة' : 'Guest Houses' }}</a>
        <a href="{{ $route('cityhotels.index') }}" @class(['is-active' => $isCityHotels])>{{ $isArabic ? 'فنادق المدينة' : 'City Hotels' }}</a>
        <a href="{{ $route('liveaboards.index') }}" @class(['is-active' => $isLiveaboards])>{{ $isArabic ? 'رحلات القوارب' : 'Liveaboards' }}</a>
        <a href="{{ $route('packages.index') }}" @class(['is-active' => $isPackages])>{{ $isArabic ? 'الباقات' : 'Packages' }}</a>
        <a href="{{ $route('blog.index') }}" @class(['is-active' => $isBlog])>{{ $isArabic ? 'المدونة' : 'Blog' }}</a>
        <a href="{{ $route('faq') }}" @class(['is-active' => $isFaq])>{{ $isArabic ? 'الأسئلة الشائعة' : 'FAQ' }}</a>
        <a href="{{ $route('about') }}" @class(['is-active' => $isAbout])>{{ $isArabic ? 'من نحن' : 'About us' }}</a>
        <a class="nav-language--menu" href="{{ $isArabic ? route('home') : route('arabic.home') }}" aria-label="{{ $isArabic ? 'Switch to English' : 'التبديل إلى العربية' }}">{{ $isArabic ? 'English' : 'العربية' }}</a>
    </nav>

    <div class="nav-actions">
        <a class="nav-language" href="{{ $isArabic ? route('home') : route('arabic.home') }}" aria-label="{{ $isArabic ? 'Switch to English' : 'التبديل إلى العربية' }}">{{ $isArabic ? 'English' : 'العربية' }}</a>
        <a class="nav-cta" href="{{ $wa }}" target="_blank">{{ $isArabic ? 'خطط لرحلتك ↗' : 'Plan my trip ↗' }}</a>
    </div>
    <button class="menu" data-menu aria-label="Toggle menu" aria-expanded="false"><span></span><span></span></button>
</header>
