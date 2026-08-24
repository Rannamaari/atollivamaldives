@extends('layouts.app')
@section('title','Maldives liveaboards — Micro Travel')
@section('description', strip_tags($page->intro ?: 'Discover liveaboard charters and voyages across the Maldives with Micro Travel.'))
@section('content')
<div class="liveaboards-page">
    @include('partials.site-nav', ['whatsAppText' => 'Hello Micro Travel, I would like help planning a Maldives liveaboard journey.'])

    <section class="liveaboards-hero" style="background-image:url('{{ $page->hero_image_url }}')">
        <div class="liveaboards-hero__shade"></div>
        <div class="liveaboards-hero__content">
            <p class="liveaboards-hero__eyebrow">{{ $page->eyebrow }}</p>
            <h1 class="liveaboards-hero__title">{{ $page->title }}</h1>
            @if($page->intro)
                <p class="liveaboards-hero__intro">{{ $page->intro }}</p>
            @endif
        </div>
    </section>

    <section class="liveaboards-story">
        <div class="liveaboards-story__inner">
            <div>
                <p class="liveaboards-story__kicker">A different kind of Maldives escape</p>
                <h2>Live on the sea. Explore more. Travel together.</h2>
            </div>
            <div class="liveaboards-story__copy">
                <p>{!! nl2br(e(strip_tags($page->body))) !!}</p>
                <a href="#contact" class="liveaboards-story__link">Start planning your charter <span>→</span></a>
            </div>
        </div>
    </section>

    <section class="liveaboards-gallery" id="gallery">
        <div class="liveaboards-section-head">
            <p class="liveaboards-section-head__kicker">ONBOARD MOMENTS</p>
            <h2>See the liveaboard<br><em>experience.</em></h2>
        </div>
        <div class="liveaboards-gallery__grid">
            @forelse($page->gallery_images ?? [] as $image)
                <figure class="liveaboards-gallery__item">
                    <img src="{{ asset('storage/'.$image) }}" alt="Liveaboard Maldives gallery image">
                </figure>
            @empty
                <figure class="liveaboards-gallery__item liveaboards-gallery__item--wide">
                    <img src="{{ $page->hero_image_url }}" alt="Liveaboard Maldives hero image">
                </figure>
            @endforelse
        </div>
    </section>

    <section class="liveaboards-fleet" id="fleet">
        <div class="liveaboards-section-head">
            <p class="liveaboards-section-head__kicker">AVAILABLE LIVEABOARDS</p>
            <h2>Liveaboards we can<br><em>help you book.</em></h2>
        </div>
        <div class="liveaboards-fleet__grid">
            @forelse($liveaboards as $liveaboard)
                @php($image = str_starts_with($liveaboard->cover_image,'http') ? $liveaboard->cover_image : asset('storage/'.$liveaboard->cover_image))
                <article class="liveaboards-fleet__card">
                    <a href="{{ route('accommodations.show',$liveaboard) }}">
                        <div class="liveaboards-fleet__photo">
                            <img src="{{ $image }}" alt="{{ $liveaboard->name }}">
                        </div>
                        <div class="liveaboards-fleet__meta">
                            <span>{{ $liveaboard->atoll }}</span>
                            <span>FROM {{ $liveaboard->currency }} {{ number_format($liveaboard->price_from) }}</span>
                        </div>
                        <h3>{{ $liveaboard->name }}</h3>
                        <p>{{ $liveaboard->summary }}</p>
                    </a>
                </article>
            @empty
                <p class="liveaboards-empty">Add published boats from the `Travel Products → Liveaboards` area in the admin panel to show them here.</p>
            @endforelse
        </div>
    </section>

    <section class="liveaboards-contact enquiry" id="contact">
        <div>
            <p class="kicker">{{ strtoupper($page->contact_heading) }}</p>
            <h2>Tell us about your<br><em>ideal voyage.</em></h2>
            <p>{{ $page->contact_text }}</p>
        </div>
        <form method="post" action="{{ route('inquiries.store') }}">
            @csrf
            @if(session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif
            <input type="hidden" name="travel_type" value="liveaboard">
            <div class="form-grid">
                <label>Your name<input name="name" required value="{{ old('name') }}"></label>
                <label>Email address<input type="email" name="email" value="{{ old('email') }}"></label>
                <label>WhatsApp number<input name="phone" required value="{{ old('phone') }}"></label>
                <label>Preferred date<input type="date" name="travel_date" value="{{ old('travel_date') }}"></label>
                <label>Travellers<input type="number" name="travellers" min="1" value="{{ old('travellers', 2) }}"></label>
                <label>Approximate budget<input name="budget" placeholder="e.g. USD 8,000" value="{{ old('budget') }}"></label>
                <label class="wide">What kind of trip would you like?<textarea name="message" rows="5" placeholder="Private charter, diving route, family trip, celebration at sea...">{{ old('message') }}</textarea></label>
            </div>
            <button>REQUEST A LIVEABOARD PLAN →</button>
        </form>
    </section>

    @include('partials.site-footer')
</div>
@endsection
