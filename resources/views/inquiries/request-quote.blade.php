@extends('layouts.app')

@section('title', 'Request a Maldives Quote — Atolliva Maldives')
@section('description', 'Tell Atolliva Maldives your dates, budget, travel style and preferences, and we will help shortlist the right Maldives stays for you.')

@php
    $quoteWhatsAppUrl = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode('Hello Atolliva Maldives, I am looking at the request quote page and would like help planning my Maldives trip.');
@endphp

@section('content')
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help finding the right Maldives stay.'])

<section class="listing-page listing-page--travel">
    <p class="kicker">REQUEST A QUOTE</p>
    <h1>Let us shortlist your<br><em>Maldives options.</em></h1>
    <p class="listing-page__intro">Share your dates, style, and priorities, and we will help you compare suitable resorts, guesthouses, city hotels, liveaboards, or tailored packages.</p>

    <section class="enquiry enquiry--quote">
        <div>
            <p class="kicker">PERSONAL TRAVEL PLANNING</p>
            <h2>Your journey,<br><em>thoughtfully shaped.</em></h2>
            <p>Use this form when you want recommendations rather than one specific property. Our team can shortlist options that fit your dates, interests, and budget.</p>
        </div>
        <form method="post" action="{{ route('inquiries.store') }}">
            @csrf
            @if(session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif
            <input type="hidden" name="source" value="request_quote">
            <div class="form-grid">
                <label>Your name<input name="name" required value="{{ old('name', trim(collect([old('first_name'), old('last_name')])->filter()->implode(' '))) }}" placeholder="How should we address you?"></label>
                <label>Email address<input type="email" name="email" value="{{ old('email') }}"></label>
                <label>Phone / WhatsApp<input name="phone" required value="{{ old('phone') }}"></label>
                <label>Nationality<select name="nationality"><option value="">Select country</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label>
                <label>Preferred property type<select name="travel_type"><option value="">Any suitable option</option><option value="resort" @selected(old('travel_type') === 'resort')>Resort</option><option value="guesthouse" @selected(old('travel_type') === 'guesthouse')>Guest House</option><option value="city_hotel" @selected(old('travel_type') === 'city_hotel')>City Hotel</option><option value="liveaboard" @selected(old('travel_type') === 'liveaboard')>Liveaboard</option><option value="package" @selected(old('travel_type') === 'package')>Package</option></select></label>
                <label>Preferred location / distance from Malé airport
                    <select name="preferred_atoll">
                        <option value="">No preference</option>
                        <option value="Near Malé / airport area - easiest access" @selected(old('preferred_atoll') === 'Near Malé / airport area - easiest access')>Near Malé / airport area - easiest access</option>
                        <option value="Close by speedboat - short transfer" @selected(old('preferred_atoll') === 'Close by speedboat - short transfer')>Close by speedboat - short transfer</option>
                        <option value="Mid-range island stay - comfortable transfer is fine" @selected(old('preferred_atoll') === 'Mid-range island stay - comfortable transfer is fine')>Mid-range island stay - comfortable transfer is fine</option>
                        <option value="Remote atoll / secluded island - happy to travel farther" @selected(old('preferred_atoll') === 'Remote atoll / secluded island - happy to travel farther')>Remote atoll / secluded island - happy to travel farther</option>
                        <option value="Best marine life / special location matters more than distance" @selected(old('preferred_atoll') === 'Best marine life / special location matters more than distance')>Best marine life / special location matters more than distance</option>
                    </select>
                </label>
                <label>Arrival date<input type="date" name="arrival_date" value="{{ old('arrival_date') }}" data-trip-arrival></label>
                <label>Departure date<input type="date" name="departure_date" value="{{ old('departure_date') }}" data-trip-departure></label>
                <label>Number of nights<input value="{{ old('arrival_date') && old('departure_date') ? \Carbon\Carbon::parse(old('arrival_date'))->diffInDays(\Carbon\Carbon::parse(old('departure_date')), false) : '' }}" readonly data-trip-nights placeholder="Calculated automatically"></label>
                <label>Adults<input type="number" name="adults" min="1" value="{{ old('adults', 2) }}"></label>
                <label>Children<input type="number" name="children" min="0" value="{{ old('children', 0) }}"></label>
                <label>Children ages<input name="children_ages" value="{{ old('children_ages') }}" placeholder="e.g. 4, 8"></label>
                <label>Meal plan
                    <select name="meal_plan">
                        <option value="">No preference</option>
                        <option value="Room Only (RO)" @selected(old('meal_plan') === 'Room Only (RO)')>Room Only (RO) - accommodation only</option>
                        <option value="Bed & Breakfast (BB)" @selected(old('meal_plan') === 'Bed & Breakfast (BB)')>Bed &amp; Breakfast (BB) - breakfast included</option>
                        <option value="Half Board (HB)" @selected(old('meal_plan') === 'Half Board (HB)')>Half Board (HB) - breakfast and dinner</option>
                        <option value="Full Board (FB)" @selected(old('meal_plan') === 'Full Board (FB)')>Full Board (FB) - breakfast, lunch and dinner</option>
                        <option value="All Inclusive (AI)" @selected(old('meal_plan') === 'All Inclusive (AI)')>All Inclusive (AI) - meals and selected drinks</option>
                        <option value="Premium All Inclusive (PAI)" @selected(old('meal_plan') === 'Premium All Inclusive (PAI)')>Premium All Inclusive (PAI) - upgraded inclusions</option>
                    </select>
                </label>
                <fieldset class="form-grid__choice-group wide">
                    <legend>Preferred transfer type</legend>
                    <div class="form-grid__choices">
                        <label class="radio-choice">
                            <input type="radio" name="transfer_preference" value="" @checked(old('transfer_preference', '') === '')>
                            <span>No preference</span>
                        </label>
                        <label class="radio-choice">
                            <input type="radio" name="transfer_preference" value="Speedboat" @checked(old('transfer_preference') === 'Speedboat')>
                            <span>Speedboat</span>
                        </label>
                        <label class="radio-choice">
                            <input type="radio" name="transfer_preference" value="Seaplane" @checked(old('transfer_preference') === 'Seaplane')>
                            <span>Seaplane</span>
                        </label>
                        <label class="radio-choice">
                            <input type="radio" name="transfer_preference" value="Domestic flight" @checked(old('transfer_preference') === 'Domestic flight')>
                            <span>Domestic flight</span>
                        </label>
                    </div>
                </fieldset>
                <label>Approximate budget<input name="budget" value="{{ old('budget') }}" placeholder="e.g. USD 5,000"></label>
                <label class="checkbox"><input type="checkbox" name="honeymoon" value="1" @checked(old('honeymoon'))>Honeymoon</label>
                <label class="checkbox"><input type="checkbox" name="family_trip" value="1" @checked(old('family_trip'))>Family trip</label>
                <label class="checkbox"><input type="checkbox" name="diving_trip" value="1" @checked(old('diving_trip'))>Diving focus</label>
                <label class="checkbox"><input type="checkbox" name="surfing_trip" value="1" @checked(old('surfing_trip'))>Surfing focus</label>
                <label class="wide">Anything else about location?<input name="preferred_room" value="{{ old('preferred_room') }}" placeholder="e.g. near great snorkelling, family-friendly island, quiet area, easy transfer with kids"></label>
                <label class="wide">Special requests<textarea name="message" rows="5" placeholder="Tell us what kind of Maldives holiday you have in mind">{{ old('message') }}</textarea></label>
            </div>
            <button>REQUEST QUOTE →</button>
        </form>
    </section>
</section>

<div class="quote-whatsapp-prompt" data-quote-whatsapp-prompt hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="quote-whatsapp-title">
    <div class="quote-whatsapp-prompt__backdrop" data-quote-whatsapp-close></div>
    <div class="quote-whatsapp-prompt__panel">
        <button class="quote-whatsapp-prompt__close" type="button" data-quote-whatsapp-close aria-label="Close WhatsApp prompt">×</button>
        <p class="quote-whatsapp-prompt__kicker">NEED A QUICKER START?</p>
        <h2 id="quote-whatsapp-title">Why not drop us a message on WhatsApp?</h2>
        <p>If you would rather talk things through instead of filling in every field, we can help you plan your Maldives trip directly on WhatsApp.</p>
        <div class="quote-whatsapp-prompt__actions">
            <a href="{{ $quoteWhatsAppUrl }}" target="_blank" rel="noopener">Message on WhatsApp ↗</a>
            <button type="button" data-quote-whatsapp-close>Continue here</button>
        </div>
    </div>
</div>

@include('partials.site-footer')
<script>
document.querySelectorAll('form[action="{{ route('inquiries.store') }}"]').forEach((form) => {
    const arrivalInput = form.querySelector('[data-trip-arrival]');
    const departureInput = form.querySelector('[data-trip-departure]');
    const nightsInput = form.querySelector('[data-trip-nights]');

    if (!arrivalInput || !departureInput || !nightsInput) {
        return;
    }

    const updateNights = () => {
        if (!arrivalInput.value || !departureInput.value) {
            nightsInput.value = '';
            return;
        }

        const arrival = new Date(`${arrivalInput.value}T00:00:00`);
        const departure = new Date(`${departureInput.value}T00:00:00`);
        const diffMs = departure.getTime() - arrival.getTime();
        const diffDays = Math.round(diffMs / 86400000);

        nightsInput.value = diffDays > 0 ? `${diffDays} night${diffDays === 1 ? '' : 's'}` : '';
    };

    arrivalInput.addEventListener('input', updateNights);
    departureInput.addEventListener('input', updateNights);
    updateNights();
});

(() => {
    const prompt = document.querySelector('[data-quote-whatsapp-prompt]');
    if (!prompt || window.sessionStorage.getItem('quote-whatsapp-prompt-dismissed') === '1') {
        return;
    }

    let promptTimer = null;

    const closePrompt = () => {
        prompt.hidden = true;
        prompt.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('quote-whatsapp-prompt-open');
        window.sessionStorage.setItem('quote-whatsapp-prompt-dismissed', '1');
        if (promptTimer) {
            window.clearTimeout(promptTimer);
        }
    };

    const openPrompt = () => {
        if (document.hidden) {
            return;
        }

        prompt.hidden = false;
        prompt.setAttribute('aria-hidden', 'false');
        document.body.classList.add('quote-whatsapp-prompt-open');
    };

    promptTimer = window.setTimeout(openPrompt, 30000);

    prompt.querySelectorAll('[data-quote-whatsapp-close]').forEach((element) => {
        element.addEventListener('click', closePrompt);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !prompt.hidden) {
            closePrompt();
        }
    });
})();
</script>
@endsection
