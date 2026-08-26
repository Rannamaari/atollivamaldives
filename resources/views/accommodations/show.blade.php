@extends('layouts.app')

@section('title', $accommodation->seo_title ?: $accommodation->name.' — Atolliva Maldives')
@section('description', $accommodation->seo_description ?: $accommodation->summary)

@section('content')
@php
    $waMessage = "Hello Atolliva Maldives,\n\nI would like to check availability for:\n\nProperty: {$accommodation->name}";
    $wa = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode($waMessage);
    $galleryImages = $accommodation->galleryImages->pluck('image_path')->filter()->map(fn ($image) => str_starts_with($image, 'http') ? $image : asset('storage/'.$image))->values();
    if ($galleryImages->isEmpty()) {
        $galleryImages = collect($accommodation->images ?? [])->filter()->map(fn ($image) => str_starts_with($image, 'http') ? $image : asset('storage/'.$image))->values();
    }
    $location = collect([$accommodation->islandRelation?->name ?: $accommodation->island, $accommodation->atollRelation?->name ?: $accommodation->atoll, $accommodation->city])->filter()->implode(', ');
    $searchArrivalDate = request('check_in');
    $searchDepartureDate = request('check_out');
    $searchAdults = max(1, (int) request('adults', 2));
    $searchChildren = max(0, (int) request('children', 0));
@endphp

@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I am interested in '.$accommodation->name.'.'])

<article class="detail detail--travel">
    <div class="detail-hero">
        <img src="{{ str_starts_with($accommodation->cover_image, 'http') ? $accommodation->cover_image : asset('storage/'.$accommodation->cover_image) }}" alt="{{ $accommodation->name }}">
    </div>

    @if($galleryImages->count() > 1)
        <section class="detail-gallery" aria-labelledby="property-gallery">
            <div class="detail-gallery__head">
                <p class="kicker">{{ strtoupper($accommodation->type->label()) }} GALLERY</p>
                <h2 id="property-gallery">See more of<br><em>{{ $accommodation->name }}</em></h2>
            </div>
            <div class="detail-gallery__grid">
                @foreach($galleryImages as $image)
                    <figure class="detail-gallery__item">
                        <img src="{{ $image }}" alt="{{ $accommodation->name }} gallery image {{ $loop->iteration }}">
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    <div class="detail-copy">
        <p class="kicker">{{ strtoupper($accommodation->type->label()) }} @if($location) · {{ $location }} @endif</p>
        <h1>{{ $accommodation->name }}</h1>
        @if($accommodation->summary)
            <p class="lead">{{ $accommodation->summary }}</p>
        @endif

        <div class="detail-summary-bar">
            @if($accommodation->rating)
                <span>{{ number_format($accommodation->rating, 1) }} star rating</span>
            @endif
            @if($accommodation->check_in_time)
                <span>Check-in {{ $accommodation->check_in_time->format('H:i') }}</span>
            @endif
            @if($accommodation->check_out_time)
                <span>Check-out {{ $accommodation->check_out_time->format('H:i') }}</span>
            @endif
            @if($accommodation->airport_distance)
                <span>{{ $accommodation->airport_distance }} from airport</span>
            @endif
        </div>

        <div class="detail-actions">
            <a class="primary" href="{{ $wa }}" target="_blank">CHECK AVAILABILITY ON WHATSAPP</a>
            <a class="outline-button" href="{{ route('inquiries.store') }}" onclick="event.preventDefault(); document.getElementById('property-inquiry').scrollIntoView({ behavior: 'smooth' });">SEND INQUIRY</a>
        </div>

        @if($accommodation->description)
            <section class="detail-section">
                <h3>Overview</h3>
                <div class="rich">{!! $accommodation->description !!}</div>
            </section>
        @endif

        @if($accommodation->facilities->isNotEmpty())
            <section class="detail-section">
                <h3>Facilities</h3>
                <div class="detail-tags">
                    @foreach($accommodation->facilities as $facility)
                        <span>{{ $facility->name }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        @if($accommodation->rooms->isNotEmpty())
            <section class="detail-section">
                <h3>Room Types</h3>
                <div class="room-grid">
                    @foreach($accommodation->rooms as $room)
                        @php
                            $roomImage = optional($room->images->first())->image_path;
                            $roomImage = $roomImage ? (str_starts_with($roomImage, 'http') ? $roomImage : asset('storage/'.$roomImage)) : (str_starts_with($accommodation->cover_image, 'http') ? $accommodation->cover_image : asset('storage/'.$accommodation->cover_image));
                        @endphp
                        <article class="room-card">
                            <img src="{{ $roomImage }}" alt="{{ $room->name }}">
                            <div class="room-card__body">
                                <div class="room-card__head">
                                    <h4>{{ $room->name }}</h4>
                                    @if($room->base_price)
                                        <p>From {{ $room->currency }} {{ number_format($room->base_price) }}</p>
                                    @endif
                                </div>
                                @if($room->short_description)
                                    <p>{{ $room->short_description }}</p>
                                @endif
                                <div class="room-card__meta">
                                    @if($room->room_size)<span>{{ $room->room_size }}</span>@endif
                                    @if($room->bed_type)<span>{{ $room->bed_type }}</span>@endif
                                    @if($room->max_occupancy)<span>Up to {{ $room->max_occupancy }} guests</span>@endif
                                </div>
                                @if($room->facilities->isNotEmpty())
                                    <div class="detail-tags detail-tags--compact">
                                        @foreach($room->facilities->take(5) as $facility)
                                            <span>{{ $facility->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if($accommodation->transfers->isNotEmpty())
            <section class="detail-section">
                <h3>Transfers</h3>
                <div class="transfer-list">
                    @foreach($accommodation->transfers as $transfer)
                        <article class="transfer-card">
                            <div>
                                <h4>{{ $transfer->name }}</h4>
                                <p>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $transfer->transfer_type)) }}@if($transfer->duration) · {{ $transfer->duration }}@endif</p>
                            </div>
                            @if($transfer->adult_price)
                                <strong>{{ $transfer->currency }} {{ number_format($transfer->adult_price) }}</strong>
                            @endif
                        </article>
                    @endforeach
                </div>
                @if($accommodation->transfer_notes)
                    <p class="detail-note">{{ $accommodation->transfer_notes }}</p>
                @endif
            </section>
        @endif

        @if($accommodation->house_rules || $accommodation->cancellation_policy)
            <section class="detail-section detail-section--two-col">
                @if($accommodation->house_rules)
                    <div>
                        <h3>House Rules</h3>
                        <div class="rich">{!! $accommodation->house_rules !!}</div>
                    </div>
                @endif
                @if($accommodation->cancellation_policy)
                    <div>
                        <h3>Cancellation Policy</h3>
                        <div class="rich">{!! $accommodation->cancellation_policy !!}</div>
                    </div>
                @endif
            </section>
        @endif
    </div>
</article>

<section class="enquiry enquiry--property" id="property-inquiry">
    <div>
        <p class="kicker">REQUEST PRICE & AVAILABILITY</p>
        <h2>Tell us your<br><em>travel plans.</em></h2>
        <p>Share your dates and preferences for {{ $accommodation->name }}, and our team will check availability and the latest selling rates for you.</p>
    </div>
    <form method="post" action="{{ route('inquiries.store') }}">
        @csrf
        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif
        <div class="form-grid">
            <label>Your name<input name="name" required value="{{ old('name') }}"></label>
            <label>WhatsApp number<input name="phone" required value="{{ old('phone') }}"></label>
            <label>Email address<input type="email" name="email" value="{{ old('email') }}"></label>
            <label>Nationality<select name="nationality"><option value="">Select country</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label>
            <input type="hidden" name="accommodation_id" value="{{ $accommodation->id }}">
            <label>Travel type<select name="travel_type"><option value="{{ $accommodation->type->value }}" selected>{{ $accommodation->type->label() }}</option></select></label>
            <label>Arrival date<input type="date" name="arrival_date" value="{{ old('arrival_date', $searchArrivalDate) }}"></label>
            <label>Departure date<input type="date" name="departure_date" value="{{ old('departure_date', $searchDepartureDate) }}"></label>
            <label>Adults<input type="number" name="adults" min="1" value="{{ old('adults', old('travellers', $searchAdults)) }}"></label>
            <label>Children<input type="number" name="children" min="0" value="{{ old('children', $searchChildren) }}"></label>
            <label>Infants<input type="number" name="infants" min="0" value="{{ old('infants', 0) }}"></label>
            <label>Approximate budget<input name="budget" placeholder="e.g. USD 3,000" value="{{ old('budget') }}"></label>
            <label>Preferred room<input name="preferred_room" value="{{ old('preferred_room') }}" placeholder="e.g. Water Villa"></label>
            <label>Meal plan<input name="meal_plan" value="{{ old('meal_plan') }}" placeholder="e.g. Half Board"></label>
            <label class="wide">Special requests<textarea name="message" rows="4" placeholder="Property: {{ $accommodation->name }}&#10;Any special requests...">{{ old('message', 'Property: '.$accommodation->name) }}</textarea></label>
        </div>
        <button>REQUEST PRICE →</button>
    </form>
</section>

@if($similarProperties->isNotEmpty())
    <section class="stays stays--similar">
        <div class="section-title">
            <div>
                <p class="kicker">YOU MAY ALSO LIKE</p>
                <h2>Similar<br><em>properties.</em></h2>
            </div>
            <p>More Maldives options selected for travellers considering {{ $accommodation->name }}.</p>
        </div>
        <div class="cards">
            @foreach($similarProperties as $property)
                @php($image = str_starts_with($property->cover_image, 'http') ? $property->cover_image : asset('storage/'.$property->cover_image))
                <article>
                    <a href="{{ route('accommodations.show', $property) }}">
                        <div class="photo"><img src="{{ $image }}" alt="{{ $property->name }}"></div>
                        <div class="meta"><span>{{ strtoupper($property->type->label()) }}</span><span>{{ $property->island }}</span></div>
                        <h3>{{ $property->name }}</h3>
                        <div class="foot"><span>{{ $property->tagline }}</span><b>FROM {{ $property->currency }} {{ number_format($property->price_from ?? 0) }}</b></div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@include('partials.site-footer')
@endsection
