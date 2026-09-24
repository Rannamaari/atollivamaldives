@extends('layouts.app')
@section('content')
@php
    $isArabic = $isArabic ?? app()->getLocale() === 'ar';
    if ($isArabic && filled($page->arabic_title)) {
        $page = clone $page;
        $page->eyebrow = $page->arabic_eyebrow ?: $page->eyebrow;
        $page->title = $page->arabic_title ?: $page->title;
        $page->intro = $page->arabic_intro ?: $page->intro;
        $page->body = $page->arabic_body ?: $page->body;
        $page->contact_heading = $page->arabic_contact_heading ?: $page->contact_heading;
        $page->contact_text = $page->arabic_contact_text ?: $page->contact_text;
    }
    $copy = $isArabic ? [
        'story_kicker' => 'نوع مختلف من رحلات المالديف',
        'story_title' => 'عِش على البحر. اكتشف أكثر. سافروا معاً.',
        'story_link' => 'ابدأ التخطيط لرحلة القارب',
        'gallery_kicker' => 'لحظات على متن القارب',
        'gallery_title' => 'شاهد تجربة<br><em>رحلة القارب.</em>',
        'fleet_kicker' => 'رحلات القوارب المتاحة',
        'fleet_title' => 'رحلات قوارب يمكننا<br><em>مساعدتك في حجزها.</em>',
        'from' => 'ابتداءً من',
        'empty' => 'أضف القوارب المنشورة من لوحة الإدارة لتظهر هنا.',
        'voyage_title' => 'أخبرنا عن<br><em>رحلتك المثالية.</em>',
        'name' => 'الاسم', 'email' => 'البريد الإلكتروني', 'phone' => 'رقم واتساب', 'nationality' => 'الجنسية',
        'country' => 'اختر الدولة', 'arrival' => 'تاريخ الوصول', 'departure' => 'تاريخ المغادرة',
        'travellers' => 'عدد المسافرين', 'budget' => 'الميزانية التقريبية',
        'trip' => 'ما نوع الرحلة التي ترغب بها؟',
        'trip_placeholder' => 'ميثاق خاص، مسار غوص، رحلة عائلية، احتفال على البحر...',
        'submit' => 'اطلب خطة رحلة قارب ←',
    ] : [
        'story_kicker' => 'A different kind of Maldives escape',
        'story_title' => 'Live on the sea. Explore more. Travel together.',
        'story_link' => 'Start planning your charter',
        'gallery_kicker' => 'ONBOARD MOMENTS',
        'gallery_title' => 'See the liveaboard<br><em>experience.</em>',
        'fleet_kicker' => 'AVAILABLE LIVEABOARDS',
        'fleet_title' => 'Liveaboards we can<br><em>help you book.</em>',
        'from' => 'FROM',
        'empty' => 'Add published boats from the Travel Products → Liveaboards area in the admin panel to show them here.',
        'voyage_title' => 'Tell us about your<br><em>ideal voyage.</em>',
        'name' => 'Your name', 'email' => 'Email address', 'phone' => 'WhatsApp number', 'nationality' => 'Nationality',
        'country' => 'Select country', 'arrival' => 'Arrival date', 'departure' => 'Departure date',
        'travellers' => 'Travellers', 'budget' => 'Approximate budget',
        'trip' => 'What kind of trip would you like?',
        'trip_placeholder' => 'Private charter, diving route, family trip, celebration at sea...',
        'submit' => 'REQUEST A LIVEABOARD PLAN →',
    ];
@endphp
<div class="liveaboards-page">
    @include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives liveaboard journey.'])

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
                <p class="liveaboards-story__kicker">{{ $copy['story_kicker'] }}</p>
                <h2>{{ $copy['story_title'] }}</h2>
            </div>
            <div class="liveaboards-story__copy">
                <p>{!! nl2br(e(strip_tags($page->body))) !!}</p>
                <a href="#contact" class="liveaboards-story__link">{{ $copy['story_link'] }} <span>{{ $isArabic ? '←' : '→' }}</span></a>
            </div>
        </div>
    </section>

    <section class="liveaboards-gallery" id="gallery">
        <div class="liveaboards-section-head">
            <p class="liveaboards-section-head__kicker">{{ $copy['gallery_kicker'] }}</p>
            <h2>{!! $copy['gallery_title'] !!}</h2>
        </div>
        <div class="liveaboards-gallery__grid">
            @forelse($page->gallery_images ?? [] as $image)
                <figure class="liveaboards-gallery__item">
                    <img src="{{ asset('storage/'.$image) }}" alt="Liveaboard Maldives gallery image" loading="lazy" decoding="async">
                </figure>
            @empty
                <figure class="liveaboards-gallery__item liveaboards-gallery__item--wide">
                    <img src="{{ $page->hero_image_url }}" alt="Liveaboard Maldives hero image" decoding="async" fetchpriority="high">
                </figure>
            @endforelse
        </div>
    </section>

    <section class="liveaboards-fleet" id="fleet">
        <div class="liveaboards-section-head">
            <p class="liveaboards-section-head__kicker">{{ $copy['fleet_kicker'] }}</p>
            <h2>{!! $copy['fleet_title'] !!}</h2>
        </div>
        <div class="liveaboards-fleet__grid">
            @forelse($liveaboards as $liveaboard)
                @php
                    $image = str_starts_with($liveaboard->cover_image,'http') ? $liveaboard->cover_image : asset('storage/'.$liveaboard->cover_image);
                    $liveaboardName = $isArabic && $liveaboard->hasArabicTranslation() ? $liveaboard->arabic_name : $liveaboard->name;
                    $liveaboardSummary = $isArabic && $liveaboard->hasArabicTranslation() ? $liveaboard->arabic_summary : $liveaboard->summary;
                @endphp
                <article class="liveaboards-fleet__card">
                    <a href="{{ $liveaboard->publicUrl() }}">
                        <div class="liveaboards-fleet__photo">
                            <img src="{{ $image }}" alt="{{ $liveaboardName }}" loading="lazy" decoding="async">
                        </div>
                        <div class="liveaboards-fleet__meta">
                            <span>{{ $liveaboard->atoll }}</span>
                            <span>{{ $copy['from'] }} {{ $liveaboard->currency }} {{ number_format($liveaboard->price_from) }}</span>
                        </div>
                        <h3>{{ $liveaboardName }}</h3>
                        <p>{{ $liveaboardSummary }}</p>
                    </a>
                </article>
            @empty
                <p class="liveaboards-empty">{{ $copy['empty'] }}</p>
            @endforelse
        </div>
    </section>

    <section class="liveaboards-contact enquiry" id="contact">
        <div>
            <p class="kicker">{{ strtoupper($page->contact_heading) }}</p>
            <h2>{!! $copy['voyage_title'] !!}</h2>
            <p>{{ $page->contact_text }}</p>
        </div>
        <form method="post" action="{{ route('inquiries.store') }}" data-recaptcha-form>
            @csrf
            @if(session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif
            @error('form')
                <p class="success" style="background:#fff3f0;color:#8a2f1e;">{{ $message }}</p>
            @enderror
            <input type="hidden" name="travel_type" value="liveaboard">
            @include('partials.recaptcha-fields', ['action' => 'liveaboard_inquiry_submit'])
            <div class="form-grid">
                <label>{{ $copy['name'] }}<input name="name" required value="{{ old('name') }}"></label>
                <label>{{ $copy['email'] }}<input type="email" name="email" value="{{ old('email') }}"></label>
                <label>{{ $copy['phone'] }}<input name="phone" required value="{{ old('phone') }}"></label>
                <label>{{ $copy['nationality'] }}<select name="nationality"><option value="">{{ $copy['country'] }}</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label>
                <label>{{ $copy['arrival'] }}<input type="date" name="arrival_date" value="{{ old('arrival_date') }}" min="{{ now()->toDateString() }}"></label>
                <label>{{ $copy['departure'] }}<input type="date" name="departure_date" value="{{ old('departure_date') }}" min="{{ now()->toDateString() }}"></label>
                <label>{{ $copy['travellers'] }}<input type="number" name="travellers" min="1" value="{{ old('travellers', 2) }}"></label>
                <label>{{ $copy['budget'] }}<input name="budget" placeholder="e.g. USD 8,000" value="{{ old('budget') }}"></label>
                <label class="wide">{{ $copy['trip'] }}<textarea name="message" rows="5" placeholder="{{ $copy['trip_placeholder'] }}">{{ old('message') }}</textarea></label>
            </div>
            <button>{{ $copy['submit'] }}</button>
        </form>
    </section>

</div>
@endsection
