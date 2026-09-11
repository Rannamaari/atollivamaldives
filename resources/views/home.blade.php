@extends('layouts.app')
@section('content')
@php($wa='https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP','9609996210').'?text='.urlencode('Hello Atolliva Maldives, I would like help planning a Maldives holiday.'))
@php($searchType = request('type', 'resort'))
@php($searchAdults = max(1, (int) request('adults', 2)))
@php($searchChildren = max(0, (int) request('children', 0)))
@php($searchRooms = max(1, (int) request('rooms', 1)))
@php($copy = static fn (string $key, string $fallback): string => $isArabic ? (string) data_get($homeCopy, $key, $fallback) : $fallback)
@php($routeName = static fn (string $name): string => ($isArabic ? 'arabic.' : '').$name)
@php($heroKicker = $isArabic ? ($hero->arabic_kicker ?: data_get($homeCopy, 'hero.kicker')) : $hero->kicker)
@php($heroHeadingOne = $isArabic ? ($hero->arabic_heading_line_one ?: data_get($homeCopy, 'hero.heading_line_one')) : $hero->heading_line_one)
@php($heroHeadingTwo = $isArabic ? ($hero->arabic_heading_line_two ?: data_get($homeCopy, 'hero.heading_line_two')) : $hero->heading_line_two)
@php($heroEmphasis = $isArabic ? ($hero->arabic_heading_emphasis ?: data_get($homeCopy, 'hero.heading_emphasis')) : $hero->heading_emphasis)
@php($heroDescription = $isArabic ? ($hero->arabic_description ?: data_get($homeCopy, 'hero.description')) : $hero->description)
<main>
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.'])
<section class="hero" style="background-image:url('{{ $hero->hero_image_url }}')"><div class="shade"></div><div class="hero-copy"><p class="kicker pale">{{ $heroKicker }}</p><h1>{{ $heroHeadingOne }}<br>{{ $heroHeadingTwo }} <em>{{ $heroEmphasis }}</em></h1><p>{{ $heroDescription }}</p></div>
<form class="finder finder--premium" method="get" action="{{ route('accommodations.index') }}" @if($isArabic) dir="rtl" @endif>
    <div class="finder__intro">
        <p class="finder__eyebrow">{{ $copy('finder.eyebrow', 'PLAN YOUR STAY') }}</p>
        <h2>{{ $copy('finder.heading', 'Find your perfect Maldives stay') }}</h2>
        <p>{{ $copy('finder.description', 'Resorts, guesthouses, liveaboards and handpicked Maldives escapes.') }}</p>
    </div>
    <div class="tabs tabs--search" role="tablist" aria-label="{{ $copy('finder.tabs_label', 'Accommodation types') }}">
        <button type="button" class="@if($searchType === 'resort') active @endif" data-tab="resort">{{ $copy('finder.tabs.0', 'Resorts') }}</button>
        <button type="button" class="@if($searchType === 'guesthouse') active @endif" data-tab="guesthouse">{{ $copy('finder.tabs.1', 'Guest Houses') }}</button>
        <button type="button" class="@if($searchType === 'liveaboard') active @endif" data-tab="liveaboard">{{ $copy('finder.tabs.2', 'Liveaboards') }}</button>
        <button type="button" class="@if($searchType === 'city_hotel') active @endif" data-tab="city_hotel">{{ $copy('finder.tabs.3', 'City Hotels') }}</button>
        <button type="button" class="@if($searchType === 'package') active @endif" data-tab="package">{{ $copy('finder.tabs.4', 'Packages') }}</button>
    </div>
    <input type="hidden" name="type" value="{{ $searchType }}">
    <input type="hidden" name="adults" value="{{ $searchAdults }}" data-guests-input="adults">
    <input type="hidden" name="children" value="{{ $searchChildren }}" data-guests-input="children">
    <input type="hidden" name="rooms" value="{{ $searchRooms }}" data-guests-input="rooms">
    <div class="fields fields--travel-search">
        <label class="search-field search-field--destination">
            <small>{{ $copy('finder.destination_label', 'Destination / Property') }}</small>
            <span class="search-field__control">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.5 18a7.5 7.5 0 1 1 5.3-2.2L21 21" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/>
                </svg>
                <input name="destination" placeholder="{{ $copy('finder.destination_placeholder', 'Resort, island, atoll, Malé...') }}" value="{{ request('destination') }}">
            </span>
        </label>
        <label class="search-field">
            <small>{{ $copy('finder.check_in', 'Check-in') }}</small>
            <span class="search-field__control">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 3v3M16 3v3M4 9h16M5 6h14a1 1 0 0 1 1 1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/>
                </svg>
                <input type="date" name="check_in" value="{{ request('check_in') }}" min="{{ now()->toDateString() }}">
            </span>
        </label>
        <label class="search-field">
            <small>{{ $copy('finder.check_out', 'Check-out') }}</small>
            <span class="search-field__control">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 3v3M16 3v3M4 9h16M5 6h14a1 1 0 0 1 1 1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/>
                </svg>
                <input type="date" name="check_out" value="{{ request('check_out') }}" min="{{ now()->toDateString() }}">
            </span>
        </label>
        <div class="search-field search-field--guests" data-guests>
            <small>{{ $copy('finder.guests', 'Guests') }}</small>
            <button
                type="button"
                class="search-field__control search-field__control--button"
                data-guests-toggle
                aria-expanded="false"
                aria-haspopup="dialog"
            >
                <span class="search-field__summary" data-guests-summary>{{ $isArabic ? $searchAdults.' بالغ · '.$searchChildren.' أطفال' : $searchAdults.' Adults · '.$searchChildren.' Children' }}</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m7 10 5 5 5-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                </svg>
            </button>
            <div class="guests-popover" data-guests-popover hidden>
                <div class="guests-row">
                    <div>
                        <strong>{{ $copy('finder.adults', 'Adults') }}</strong>
                        <span>{{ $copy('finder.adults_help', 'Ages 13+') }}</span>
                    </div>
                    <div class="guests-stepper">
                        <button type="button" data-stepper-target="adults" data-stepper-direction="-1" aria-label="Decrease adults">−</button>
                        <span data-stepper-value="adults">{{ $searchAdults }}</span>
                        <button type="button" data-stepper-target="adults" data-stepper-direction="1" aria-label="Increase adults">+</button>
                    </div>
                </div>
                <div class="guests-row">
                    <div>
                        <strong>{{ $copy('finder.children', 'Children') }}</strong>
                        <span>{{ $copy('finder.children_help', 'Ages 0–12') }}</span>
                    </div>
                    <div class="guests-stepper">
                        <button type="button" data-stepper-target="children" data-stepper-direction="-1" aria-label="Decrease children">−</button>
                        <span data-stepper-value="children">{{ $searchChildren }}</span>
                        <button type="button" data-stepper-target="children" data-stepper-direction="1" aria-label="Increase children">+</button>
                    </div>
                </div>
                <div class="guests-row">
                    <div>
                        <strong>{{ $copy('finder.rooms', 'Rooms') }}</strong>
                        <span>{{ $copy('finder.rooms_help', 'For added comfort') }}</span>
                    </div>
                    <div class="guests-stepper">
                        <button type="button" data-stepper-target="rooms" data-stepper-direction="-1" aria-label="Decrease rooms">−</button>
                        <span data-stepper-value="rooms">{{ $searchRooms }}</span>
                        <button type="button" data-stepper-target="rooms" data-stepper-direction="1" aria-label="Increase rooms">+</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="search-submit" type="submit">{{ $copy('finder.search', 'Search') }} <span>→</span></button>
    </div>
</form></section>
<section class="home-categories" aria-labelledby="home-categories-title">
    <div class="home-categories__inner">
        <div class="home-categories__heading">
            <p class="kicker">{{ $copy('explore.kicker', $hero->explore_kicker) }}</p>
            <h2 id="home-categories-title">{{ $copy('explore.heading_line_one', $hero->explore_heading_line_one) }}<br><em>{{ $copy('explore.heading_emphasis', $hero->explore_heading_emphasis) }}</em></h2>
        </div>
        <div class="home-categories__grid">
            @foreach($exploreCards as $card)
                <a class="home-categories__card" href="{{ $card['href'] }}">
                    <div class="home-categories__image-wrap">
                        <img class="home-categories__image" src="{{ $card['image'] }}" alt="{{ $card['label'] }} travel experiences in the Maldives" loading="lazy" decoding="async">
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
<section class="intro" id="about"><div><p class="kicker">{{ $copy('intro.kicker', 'MORE THAN A HOLIDAY') }}</p><h2>{{ $copy('intro.heading_line_one', 'The Maldives,') }}<br><em>{{ $copy('intro.heading_emphasis', 'made personal.') }}</em></h2></div><div><p>{{ $copy('intro.description', 'From your first question to your final seaplane, we make every detail feel effortless. Our Maldives-based team uses local knowledge to create a journey that feels entirely yours.') }}</p><a class="under" href="{{ $wa }}" target="_blank">{{ $copy('intro.cta', 'TALK TO A LOCAL EXPERT') }} <span>→</span></a></div></section>
<x-why-book-with-micro-travel :heading="$copy('benefits_heading', 'WHY BOOK WITH ATOLLIVA MALDIVES?')" :benefits="$isArabic ? data_get($homeCopy, 'benefits') : null" />
<section class="stays" id="products"><div class="section-title"><div><p class="kicker">{{ $copy('products.kicker', 'TRAVEL PRODUCTS') }}</p><h2>{{ $copy('products.heading_line_one', 'Resorts, journeys,') }}<br><em>{{ $copy('products.heading_emphasis', 'and island stays.') }}</em></h2></div><p>{{ $copy('products.description', 'Featured resorts, guest houses, city hotels, packages, and liveaboards chosen for their character, care, and sense of place.') }}</p></div><div class="cards">
@forelse($featuredProducts as $product) @php($image=str_starts_with($product->cover_image,'http')?$product->cover_image:asset('storage/'.$product->cover_image))
<article><a href="{{ $product->publicUrl() }}"><div class="photo"><img src="{{ $image }}" alt="{{ $product->name }} in the Maldives" loading="lazy" decoding="async">@if($product->type === \App\Enums\AccommodationType::Package && $product->package_best_seller)<span class="package-card-badge">Best Seller</span>@endif<small>{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</small></div><div class="meta"><span>{{ strtoupper($product->type->label()) }}</span><span>{{ $product->island }}</span></div><h3>{{ $product->name }}</h3><div class="foot"><span>{{ $product->packageGuestInclusionLabel() ?: $product->tagline }}</span><b>FROM {{ $product->currentDisplayCurrency() }} {{ number_format($product->currentDisplayPrice() ?? 0) }}</b></div></a></article>
@empty <p>Add featured travel products from the admin panel.</p> @endforelse
 </div><a class="outline" href="{{ route($routeName('resorts.index')) }}">{{ $copy('products.explore_cta', 'EXPLORE ALL PRODUCTS') }} <span>→</span></a></section>
<section class="experience" id="experiences"><div class="exp-image" style="background-image: url('{{ $hero->experience_image_url }}')"><span>▶</span></div><div class="exp-copy"><p class="kicker pale">{{ $copy('experience.kicker', 'BEYOND THE BLUE') }}</p><h2>{{ $copy('experience.heading_line_one', 'Come for the islands.') }}<br><em>{{ $copy('experience.heading_emphasis', 'Remember the feeling.') }}</em></h2><p>{{ $copy('experience.description', 'Drift with manta rays. Share breakfast on an empty sandbank. Learn the rhythm of island life from the people who call it home.') }}</p><a class="under" href="{{ $wa }}" target="_blank">{{ $copy('experience.cta', 'DISCOVER EXPERIENCES') }} <span>→</span></a></div></section>
<section class="journal"><div class="section-title"><div><p class="kicker">{{ $copy('journal.kicker', 'THE BLOG') }}</p><h2>{{ $copy('journal.heading_line_one', 'Stories from') }}<br><em>{{ $copy('journal.heading_emphasis', 'the islands.') }}</em></h2></div><a class="under" href="{{ route($routeName('blog.index')) }}">{{ $copy('journal.cta', 'VIEW ALL POSTS') }} →</a></div><div class="posts">@foreach($posts as $post) @php($postImage=str_starts_with($post->featured_image ?? '','http')?$post->featured_image:asset('storage/'.$post->featured_image))<article><a href="{{ $isArabic && $post->hasArabicTranslation() ? route('blog.arabic.show', $post) : route('blog.show', $post) }}"><img src="{{ $postImage }}" alt="{{ $post->title }} featured image" loading="lazy" decoding="async"><p class="kicker">{{ strtoupper($post->category) }}</p><h3>{{ $isArabic && $post->hasArabicTranslation() ? $post->arabic_title : $post->title }}</h3><p>{{ $isArabic && $post->hasArabicTranslation() ? $post->arabic_excerpt : $post->excerpt }}</p><span>{{ $copy('journal.read_cta', 'READ POST') }} →</span></a></article>@endforeach</div></section>
<section class="enquiry"><div><p class="kicker">{{ $copy('inquiry.kicker', 'PERSONAL TRAVEL PLANNING') }}</p><h2>{{ $copy('inquiry.heading_line_one', 'Tell us what you') }}<br><em>{{ $copy('inquiry.heading_emphasis', 'have in mind.') }}</em></h2><p>{{ $copy('inquiry.description', 'Share a few details and our team will help shape the right Maldives journey for you.') }}</p></div><form method="post" action="{{ route('inquiries.store') }}" data-recaptcha-form @if($isArabic) dir="rtl" @endif>@csrf @if(session('success'))<p class="success">{{ session('success') }}</p>@endif @error('form')<p class="success" style="background:#fff3f0;color:#8a2f1e;">{{ $message }}</p>@enderror @include('partials.recaptcha-fields', ['action' => 'homepage_inquiry_submit'])<div class="form-grid"><label>{{ $copy('inquiry.name', 'Your name') }}<input name="name" required value="{{ old('name') }}"></label><label>{{ $copy('inquiry.phone', 'WhatsApp number') }}<input name="phone" required value="{{ old('phone') }}"></label><label>{{ $copy('inquiry.nationality', 'Nationality') }}<select name="nationality"><option value="">{{ $copy('inquiry.country_placeholder', 'Select country') }}</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label><label>{{ $copy('inquiry.travel_type', 'Travel type') }}<select name="travel_type"><option value="resort">{{ $copy('finder.tabs.0', 'Resort') }}</option><option value="guesthouse">{{ $copy('finder.tabs.1', 'Guest House') }}</option><option value="liveaboard">{{ $copy('finder.tabs.2', 'Liveaboard') }}</option><option value="city_hotel">{{ $copy('finder.tabs.3', 'City Hotel') }}</option><option value="package">{{ $copy('finder.tabs.4', 'Package') }}</option></select></label><label>{{ $copy('inquiry.arrival', 'Arrival date') }}<input type="date" name="arrival_date" value="{{ old('arrival_date') }}" min="{{ now()->toDateString() }}"></label><label>{{ $copy('inquiry.departure', 'Departure date') }}<input type="date" name="departure_date" value="{{ old('departure_date') }}" min="{{ now()->toDateString() }}"></label><label>{{ $copy('inquiry.travellers', 'Travellers') }}<input type="number" name="travellers" min="1" value="{{ old('travellers', 2) }}"></label><label>{{ $copy('inquiry.budget', 'Approximate budget') }}<input name="budget" placeholder="{{ $copy('inquiry.budget_placeholder', 'e.g. USD 3,000') }}" value="{{ old('budget') }}"></label><label class="wide">{{ $copy('inquiry.message', 'Anything else?') }}<textarea name="message" rows="4" placeholder="{{ $copy('inquiry.message_placeholder', 'Tell us about your ideal trip') }}">{{ old('message') }}</textarea></label></div><button>{{ $copy('inquiry.submit', 'REQUEST A TRIP PLAN') }} →</button></form></section>
<section class="closing"><p class="kicker pale">{{ $copy('closing.kicker', 'YOUR JOURNEY BEGINS HERE') }}</p><h2>{{ $copy('closing.heading_line_one', 'Let’s create something') }}<br><em>{{ $copy('closing.heading_emphasis', 'unforgettable.') }}</em></h2><a href="{{ $wa }}" target="_blank">{{ $copy('closing.cta', 'START PLANNING ON WHATSAPP') }} <span>↗</span></a></section>
@include('partials.site-footer')<a class="mobile-cta" href="{{ $wa }}" target="_blank">PLAN ON WHATSAPP <span>↗</span></a>
</main>@endsection
