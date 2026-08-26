@extends('layouts.app')

@section('title', 'Request a Maldives Quote — Atolliva Maldives')
@section('description', 'Tell Atolliva Maldives your dates, budget, travel style and preferences, and we will help shortlist the right Maldives stays for you.')

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
                <label>First name<input name="first_name" required value="{{ old('first_name') }}"></label>
                <label>Last name<input name="last_name" value="{{ old('last_name') }}"></label>
                <label>Email address<input type="email" name="email" value="{{ old('email') }}"></label>
                <label>Phone / WhatsApp<input name="phone" required value="{{ old('phone') }}"></label>
                <label>Nationality<select name="nationality"><option value="">Select country</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label>
                <label>Preferred property type<select name="travel_type"><option value="">Any suitable option</option><option value="resort" @selected(old('travel_type') === 'resort')>Resort</option><option value="guesthouse" @selected(old('travel_type') === 'guesthouse')>Guest House</option><option value="city_hotel" @selected(old('travel_type') === 'city_hotel')>City Hotel</option><option value="liveaboard" @selected(old('travel_type') === 'liveaboard')>Liveaboard</option><option value="package" @selected(old('travel_type') === 'package')>Package</option></select></label>
                <label>Preferred atoll<input name="preferred_atoll" value="{{ old('preferred_atoll') }}" placeholder="e.g. Baa Atoll"></label>
                <label>Arrival date<input type="date" name="arrival_date" value="{{ old('arrival_date') }}"></label>
                <label>Departure date<input type="date" name="departure_date" value="{{ old('departure_date') }}"></label>
                <label>Adults<input type="number" name="adults" min="1" value="{{ old('adults', 2) }}"></label>
                <label>Children<input type="number" name="children" min="0" value="{{ old('children', 0) }}"></label>
                <label>Children ages<input name="children_ages" value="{{ old('children_ages') }}" placeholder="e.g. 4, 8"></label>
                <label>Meal plan<select name="meal_plan"><option value="">No preference</option><option value="RO" @selected(old('meal_plan') === 'RO')>RO</option><option value="BB" @selected(old('meal_plan') === 'BB')>BB</option><option value="HB" @selected(old('meal_plan') === 'HB')>HB</option><option value="FB" @selected(old('meal_plan') === 'FB')>FB</option><option value="AI" @selected(old('meal_plan') === 'AI')>AI</option><option value="PAI" @selected(old('meal_plan') === 'PAI')>PAI</option></select></label>
                <label>Transfer preference<input name="transfer_preference" value="{{ old('transfer_preference') }}" placeholder="e.g. Speedboat only"></label>
                <label>Approximate budget<input name="budget" value="{{ old('budget') }}" placeholder="e.g. USD 5,000"></label>
                <label class="checkbox"><input type="checkbox" name="honeymoon" value="1" @checked(old('honeymoon'))>Honeymoon</label>
                <label class="checkbox"><input type="checkbox" name="family_trip" value="1" @checked(old('family_trip'))>Family trip</label>
                <label class="checkbox"><input type="checkbox" name="diving_trip" value="1" @checked(old('diving_trip'))>Diving focus</label>
                <label class="checkbox"><input type="checkbox" name="surfing_trip" value="1" @checked(old('surfing_trip'))>Surfing focus</label>
                <label class="wide">Special requests<textarea name="message" rows="5" placeholder="Tell us what kind of Maldives holiday you have in mind">{{ old('message') }}</textarea></label>
            </div>
            <button>REQUEST QUOTE →</button>
        </form>
    </section>
</section>

@include('partials.site-footer')
@endsection
