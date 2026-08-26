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
@endphp

<header class="{{ $class ?? '' }}">
    @include('partials.logo', ['dark' => $dark ?? false])

    <nav data-nav>
        <a href="{{ route('resorts.index') }}" @class(['is-active' => $isResorts])>Resorts</a>
        <a href="{{ route('guesthouses.index') }}" @class(['is-active' => $isGuestHouses])>Guest Houses</a>
        <a href="{{ route('cityhotels.index') }}" @class(['is-active' => $isCityHotels])>City Hotels</a>
        <a href="{{ route('liveaboards.index') }}" @class(['is-active' => $isLiveaboards])>Liveaboards</a>
        <a href="{{ route('packages.index') }}" @class(['is-active' => $isPackages])>Packages</a>
        <a href="{{ route('blog.index') }}" @class(['is-active' => $isBlog])>Blog</a>
        <a href="{{ route('faq') }}" @class(['is-active' => $isFaq])>FAQ</a>
        <a href="{{ route('about') }}" @class(['is-active' => $isAbout])>About us</a>
    </nav>

    <a class="nav-cta" href="{{ $wa }}" target="_blank">Plan my trip ↗</a>
    <button class="menu" data-menu aria-label="Toggle menu" aria-expanded="false"><span></span><span></span></button>
</header>
