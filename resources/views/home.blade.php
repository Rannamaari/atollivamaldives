@extends('layouts.app')
@section('title','Atolliva Maldives')
@section('description','YOUR MALDIVES, THOUGHTFULLY PLANNED')
@section('content')
@php($wa='https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP','9609996210').'?text='.urlencode('Hello Atolliva Maldives, I would like help planning a Maldives holiday.'))
@php($searchType = request('type', 'resort'))
@php($searchAdults = max(1, (int) request('adults', 2)))
@php($searchChildren = max(0, (int) request('children', 0)))
@php($searchRooms = max(1, (int) request('rooms', 1)))
<main>
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.'])
<section class="hero" style="background-image:url('{{ $hero->hero_image_url }}')"><div class="shade"></div><div class="hero-copy"><p class="kicker pale">{{ $hero->kicker }}</p><h1>{{ $hero->heading_line_one }}<br>{{ $hero->heading_line_two }} <em>{{ $hero->heading_emphasis }}</em></h1><p>{{ $hero->description }}</p></div>
<form class="finder finder--premium" method="get" action="{{ route('accommodations.index') }}">
    <div class="finder__intro">
        <p class="finder__eyebrow">PLAN YOUR STAY</p>
        <h2>Find your perfect Maldives stay</h2>
        <p>Resorts, guesthouses, liveaboards and handpicked Maldives escapes.</p>
    </div>
    <div class="tabs tabs--search" role="tablist" aria-label="Accommodation types">
        <button type="button" class="@if($searchType === 'resort') active @endif" data-tab="resort">Resorts</button>
        <button type="button" class="@if($searchType === 'guesthouse') active @endif" data-tab="guesthouse">Guest Houses</button>
        <button type="button" class="@if($searchType === 'liveaboard') active @endif" data-tab="liveaboard">Liveaboards</button>
        <button type="button" class="@if($searchType === 'city_hotel') active @endif" data-tab="city_hotel">City Hotels</button>
        <button type="button" class="@if($searchType === 'package') active @endif" data-tab="package">Packages</button>
    </div>
    <input type="hidden" name="type" value="{{ $searchType }}">
    <input type="hidden" name="adults" value="{{ $searchAdults }}" data-guests-input="adults">
    <input type="hidden" name="children" value="{{ $searchChildren }}" data-guests-input="children">
    <input type="hidden" name="rooms" value="{{ $searchRooms }}" data-guests-input="rooms">
    <div class="fields fields--travel-search">
        <label class="search-field search-field--destination">
            <small>Destination / Property</small>
            <span class="search-field__control">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.5 18a7.5 7.5 0 1 1 5.3-2.2L21 21" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/>
                </svg>
                <input name="destination" placeholder="Resort, island, atoll, Malé..." value="{{ request('destination') }}">
            </span>
        </label>
        <label class="search-field">
            <small>Check-in</small>
            <span class="search-field__control">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 3v3M16 3v3M4 9h16M5 6h14a1 1 0 0 1 1 1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/>
                </svg>
                <input type="date" name="check_in" value="{{ request('check_in') }}">
            </span>
        </label>
        <label class="search-field">
            <small>Check-out</small>
            <span class="search-field__control">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 3v3M16 3v3M4 9h16M5 6h14a1 1 0 0 1 1 1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/>
                </svg>
                <input type="date" name="check_out" value="{{ request('check_out') }}">
            </span>
        </label>
        <div class="search-field search-field--guests" data-guests>
            <small>Guests</small>
            <button
                type="button"
                class="search-field__control search-field__control--button"
                data-guests-toggle
                aria-expanded="false"
                aria-haspopup="dialog"
            >
                <span class="search-field__summary" data-guests-summary>{{ $searchAdults }} Adults · {{ $searchChildren }} Children</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m7 10 5 5 5-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                </svg>
            </button>
            <div class="guests-popover" data-guests-popover hidden>
                <div class="guests-row">
                    <div>
                        <strong>Adults</strong>
                        <span>Ages 13+</span>
                    </div>
                    <div class="guests-stepper">
                        <button type="button" data-stepper-target="adults" data-stepper-direction="-1" aria-label="Decrease adults">−</button>
                        <span data-stepper-value="adults">{{ $searchAdults }}</span>
                        <button type="button" data-stepper-target="adults" data-stepper-direction="1" aria-label="Increase adults">+</button>
                    </div>
                </div>
                <div class="guests-row">
                    <div>
                        <strong>Children</strong>
                        <span>Ages 0–12</span>
                    </div>
                    <div class="guests-stepper">
                        <button type="button" data-stepper-target="children" data-stepper-direction="-1" aria-label="Decrease children">−</button>
                        <span data-stepper-value="children">{{ $searchChildren }}</span>
                        <button type="button" data-stepper-target="children" data-stepper-direction="1" aria-label="Increase children">+</button>
                    </div>
                </div>
                <div class="guests-row">
                    <div>
                        <strong>Rooms</strong>
                        <span>For added comfort</span>
                    </div>
                    <div class="guests-stepper">
                        <button type="button" data-stepper-target="rooms" data-stepper-direction="-1" aria-label="Decrease rooms">−</button>
                        <span data-stepper-value="rooms">{{ $searchRooms }}</span>
                        <button type="button" data-stepper-target="rooms" data-stepper-direction="1" aria-label="Increase rooms">+</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="search-submit" type="submit">Search <span>→</span></button>
    </div>
</form></section>
<section class="home-categories" aria-labelledby="home-categories-title">
    <div class="home-categories__inner">
        <div class="home-categories__heading">
            <p class="kicker">{{ $hero->explore_kicker }}</p>
            <h2 id="home-categories-title">{{ $hero->explore_heading_line_one }}<br><em>{{ $hero->explore_heading_emphasis }}</em></h2>
        </div>
        <div class="home-categories__grid">
            @foreach($exploreCards as $card)
                <a class="home-categories__card" href="{{ $card['href'] }}">
                    <div class="home-categories__image-wrap">
                        <img class="home-categories__image" src="{{ $card['image'] }}" alt="{{ $card['label'] }}">
                    </div>
                    <div class="home-categories__content">
                        <span>{{ $card['count'] }} {{ $card['label'] }}</span>
                        <strong>{{ $card['description'] }}</strong>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
<section class="intro" id="about"><div><p class="kicker">MORE THAN A HOLIDAY</p><h2>The Maldives,<br><em>made personal.</em></h2></div><div><p>From your first question to your final seaplane, we make every detail feel effortless. Our Maldives-based team uses local knowledge to create a journey that feels entirely yours.</p><a class="under" href="{{ $wa }}" target="_blank">TALK TO A LOCAL EXPERT <span>→</span></a></div></section>
<x-why-book-with-micro-travel />
<section class="stays" id="products"><div class="section-title"><div><p class="kicker">TRAVEL PRODUCTS</p><h2>Resorts, journeys,<br><em>and island stays.</em></h2></div><p>Featured resorts, guest houses, city hotels, packages, and liveaboards chosen for their character, care, and sense of place.</p></div><div class="cards">
@forelse($featuredProducts as $product) @php($image=str_starts_with($product->cover_image,'http')?$product->cover_image:asset('storage/'.$product->cover_image))
<article><a href="{{ route('accommodations.show',$product) }}"><div class="photo"><img src="{{ $image }}" alt="{{ $product->name }}"><small>{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</small></div><div class="meta"><span>{{ strtoupper($product->type->label()) }}</span><span>{{ $product->island }}</span></div><h3>{{ $product->name }}</h3><div class="foot"><span>{{ $product->tagline }}</span><b>FROM {{ $product->currency }} {{ number_format($product->price_from) }}</b></div></a></article>
@empty <p>Add featured travel products from the admin panel.</p> @endforelse
</div><a class="outline" href="{{ route('accommodations.index') }}">EXPLORE ALL PRODUCTS <span>→</span></a></section>
<section class="experience" id="experiences"><div class="exp-image"><span>▶</span></div><div class="exp-copy"><p class="kicker pale">BEYOND THE BLUE</p><h2>Come for the islands.<br><em>Remember the feeling.</em></h2><p>Drift with manta rays. Share breakfast on an empty sandbank. Learn the rhythm of island life from the people who call it home.</p><a class="under" href="{{ $wa }}" target="_blank">DISCOVER EXPERIENCES <span>→</span></a></div></section>
<section class="journal"><div class="section-title"><div><p class="kicker">THE BLOG</p><h2>Stories from<br><em>the islands.</em></h2></div><a class="under" href="{{ route('blog.index') }}">VIEW ALL POSTS →</a></div><div class="posts">@foreach($posts as $post) @php($postImage=str_starts_with($post->featured_image ?? '','http')?$post->featured_image:asset('storage/'.$post->featured_image))<article><a href="{{ route('blog.show',$post) }}"><img src="{{ $postImage }}" alt=""><p class="kicker">{{ strtoupper($post->category) }}</p><h3>{{ $post->title }}</h3><p>{{ $post->excerpt }}</p><span>READ POST →</span></a></article>@endforeach</div></section>
<section class="enquiry"><div><p class="kicker">PERSONAL TRAVEL PLANNING</p><h2>Tell us what you<br><em>have in mind.</em></h2><p>Share a few details and our team will help shape the right Maldives journey for you.</p></div><form method="post" action="{{ route('inquiries.store') }}" data-recaptcha-form>@csrf @if(session('success'))<p class="success">{{ session('success') }}</p>@endif @error('form')<p class="success" style="background:#fff3f0;color:#8a2f1e;">{{ $message }}</p>@enderror @include('partials.recaptcha-fields', ['action' => 'homepage_inquiry_submit'])<div class="form-grid"><label>Your name<input name="name" required value="{{ old('name') }}"></label><label>WhatsApp number<input name="phone" required value="{{ old('phone') }}"></label><label>Nationality<select name="nationality"><option value="">Select country</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label><label>Travel type<select name="travel_type"><option value="resort">Resort</option><option value="guesthouse">Guest House</option><option value="liveaboard">Liveaboard</option><option value="city_hotel">City Hotel</option><option value="package">Package</option></select></label><label>Arrival date<input type="date" name="arrival_date" value="{{ old('arrival_date') }}"></label><label>Departure date<input type="date" name="departure_date" value="{{ old('departure_date') }}"></label><label>Travellers<input type="number" name="travellers" min="1" value="{{ old('travellers', 2) }}"></label><label>Approximate budget<input name="budget" placeholder="e.g. USD 3,000" value="{{ old('budget') }}"></label><label class="wide">Anything else?<textarea name="message" rows="4" placeholder="Tell us about your ideal trip">{{ old('message') }}</textarea></label></div><button>REQUEST A TRIP PLAN →</button></form></section>
<section class="closing"><p class="kicker pale">YOUR JOURNEY BEGINS HERE</p><h2>Let’s create something<br><em>unforgettable.</em></h2><a href="{{ $wa }}" target="_blank">START PLANNING ON WHATSAPP <span>↗</span></a></section>
@include('partials.site-footer')<a class="mobile-cta" href="{{ $wa }}" target="_blank">PLAN ON WHATSAPP <span>↗</span></a>
</main>@endsection
