@php
    $wa = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode($whatsAppText ?? 'Hello Micro Travel, I would like help planning a Maldives holiday.');
    $isHome = request()->routeIs('home');
    $isLiveaboards = request()->routeIs('liveaboards.*');
    $isBlog = request()->routeIs('blog.*');
    $isStays = request()->routeIs('accommodations.*');
    $homeSection = fn (string $hash) => $isHome ? $hash : route('home').$hash;
@endphp

<header class="{{ $class ?? '' }}">
    @include('partials.logo', ['dark' => $dark ?? false])

    <nav data-nav>
        <a href="{{ $homeSection('#stays') }}" @class(['is-active' => $isStays])>Stays</a>
        <a href="{{ route('liveaboards.index') }}" @class(['is-active' => $isLiveaboards])>Liveaboards</a>
        <a href="{{ $homeSection('#experiences') }}">Experiences</a>
        <a href="{{ route('blog.index') }}" @class(['is-active' => $isBlog])>Blog</a>
        <a href="{{ $homeSection('#about') }}">About us</a>
    </nav>

    <a class="nav-cta" href="{{ $wa }}" target="_blank">Plan my trip ↗</a>
    <button class="menu" data-menu aria-label="Toggle menu"><span></span><span></span></button>
</header>
