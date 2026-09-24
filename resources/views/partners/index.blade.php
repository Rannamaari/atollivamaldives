@extends('layouts.app')

@section('title', 'Maldives B2B Travel Partner & DMC | Atolliva Maldives')
@section('description', 'Send Atolliva Maldives one genuine enquiry for competitive B2B accommodation rates, transfers, experiences and practical Maldives ground handling support.')
@section('canonical', route('partners.index'))
@section('share_image', $homePage->hero_image_url)

@section('content')
@php
    $whatsAppUrl = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode('Hello Atolliva Maldives, I have a B2B Maldives enquiry I would like your team to quote.');
    $benefits = [
        ['Competitive B2B Rates', 'We source rates from Maldives resorts, hotels, guesthouses and local suppliers.'],
        ['Fast, Clear Quotations', 'Straightforward options with accommodation, meals, transfers and inclusions clearly shown.'],
        ['Local Maldives Support', "We're here on the ground to help before and during your client's stay."],
        ['Your Client Stays Your Client', 'We work behind the scenes and respect your relationship with your customer.'],
    ];
    $services = [
        'Luxury Resorts', 'Guesthouses', 'City Hotels', 'Honeymoons', 'Family Holidays',
        'Speedboat Transfers', 'Seaplane Transfers', 'Domestic Transfers', 'Diving',
        'Surf Holidays', 'Excursions', 'Multi-Island Trips', 'Special Celebrations', 'Custom Itineraries',
    ];
@endphp

<main class="partners-page partners-page--concise">
    <section class="partners-hero partners-hero--concise" style="--partners-hero: url('{{ $homePage->hero_image_url }}')">
        @include('partials.site-nav', ['class' => 'inner-nav partners-nav', 'dark' => true, 'whatsAppText' => 'Hello Atolliva Maldives, I have a B2B Maldives enquiry I would like your team to quote.'])
        <div class="partners-hero__content partners-container" data-reveal>
            <p class="partners-eyebrow partners-eyebrow--light">For travel professionals</p>
            <h1>You bring the traveller.<br><em>We take care of the Maldives.</em></h1>
            <p class="partners-hero__intro">Competitive B2B rates, transfers, experiences and tailor-made Maldives holidays — handled by a local team.</p>
            <div class="partners-actions">
                <a class="partners-button partners-button--primary" href="#partner-form">Send Us an Enquiry <span>→</span></a>
                <a class="partners-button partners-button--ghost" href="#partner-form">Partner With Us <span>↘</span></a>
            </div>
            <p class="partners-hero__trade">Travel Agencies <i></i> Tour Operators <i></i> Travel Advisors <i></i> DMCs</p>
        </div>
    </section>

    <section class="partners-test partners-test--focus" id="first-enquiry">
        <div class="partners-container partners-test__grid">
            <div data-reveal>
                <p class="partners-eyebrow partners-eyebrow--aqua">A simple way to start</p>
                <h2>Give us <em>one enquiry.</em></h2>
                <p>You don't need to change your existing Maldives supplier or make any commitment. Send us one genuine enquiry and see how we work.</p>
                <a class="partners-button partners-button--aqua" href="#partner-form">Send Your First Enquiry <span>→</span></a>
            </div>
            <div class="partners-flow" aria-label="Information to send Atolliva" data-reveal>
                @foreach(['Dates', 'Guests', 'Budget', 'Preferences', 'Atolliva'] as $step)
                    <span @class(['is-final' => $loop->last])>{{ $step }}</span>
                    @unless($loop->last)<b aria-hidden="true">→</b>@endunless
                @endforeach
                <p>We'll source suitable options and send you a clear B2B quotation. Compare our rates, response and service for yourself.</p>
            </div>
        </div>
    </section>

    <section class="partners-section partners-container partners-why" data-reveal>
        <div class="partners-section__heading">
            <div>
                <p class="partners-eyebrow">Why work with Atolliva</p>
                <h2>Practical Maldives support<br><em>for your agency.</em></h2>
            </div>
        </div>
        <div class="partners-benefits partners-benefits--four">
            @foreach($benefits as [$title, $description])
                <article class="partners-benefit partners-benefit--simple">
                    <span class="partners-benefit__number">0{{ $loop->iteration }}</span>
                    <h3>{{ $title }}</h3>
                    <p>{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="partners-services">
        <div class="partners-container" data-reveal>
            <div class="partners-arrange__heading">
                <div>
                    <p class="partners-eyebrow">What we handle</p>
                    <h2>One Maldives partner.<br><em>More possibilities.</em></h2>
                </div>
                <p>From airport arrival to the final transfer, we can bring the Maldives side of the trip together.</p>
            </div>
            <div class="partners-service-list">
                @foreach($services as $service)
                    <span><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 10 3 3 7-7"/></svg>{{ $service }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <section class="partners-quote" style="--partners-final: url('{{ $homePage->resorts_card_image_url ?: $homePage->hero_image_url }}')">
        <div class="partners-container partners-quote__inner" data-reveal>
            <p class="partners-eyebrow partners-eyebrow--light">Have an enquiry ready?</p>
            <h2>Need a Maldives quote?<br><em>Send it to us.</em></h2>
            <p>Share the dates, number of guests, budget and preferences. We'll check suitable options and come back with a clear B2B quotation.</p>
            <div class="partners-actions">
                <a class="partners-button partners-button--primary" href="#partner-form">Send a B2B Enquiry <span>→</span></a>
                <a class="partners-button partners-button--ghost" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">WhatsApp Our Team <span>↗</span></a>
            </div>
        </div>
    </section>

    <section class="partners-form-section" id="partner-form">
        <div class="partners-container partners-form-layout">
            <div class="partners-form-intro" data-reveal>
                <p class="partners-eyebrow partners-eyebrow--aqua">Maldives B2B enquiries</p>
                <h2>Let's work<br><em>together.</em></h2>
                <p>Send us an enquiry or tell us briefly about your business. We'll reply personally.</p>
                <div class="partners-contact-note">
                    <span>Prefer WhatsApp?</span>
                    <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">Message our Maldives team ↗</a>
                </div>
            </div>
            <form class="partners-form" method="post" action="{{ route('partners.store') }}" data-recaptcha-form data-reveal>
                @csrf
                @if(session('partner_success'))
                    <div class="partners-form__success" role="status"><strong>Enquiry received.</strong><p>{{ session('partner_success') }}</p></div>
                @endif
                @if($errors->any())
                    <div class="partners-form__errors" role="alert"><strong>Please check the highlighted details.</strong><p>{{ $errors->first() }}</p></div>
                @endif
                @include('partials.recaptcha-fields', ['action' => 'partner_application_submit', 'honeypot' => 'company_fax'])
                <div class="partners-form__grid">
                    <label>Company Name *<input name="company" value="{{ old('company') }}" required autocomplete="organization"></label>
                    <label>Contact Person *<input name="contact_name" value="{{ old('contact_name') }}" required autocomplete="name"></label>
                    <label>Country *<select name="country" required><option value="">Select country</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('country') === $country)>{{ $country }}</option>@endforeach</select></label>
                    <label>Email *<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
                    <label>WhatsApp / Phone *<input type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"></label>
                    <label>Company Website <small>Optional</small><input type="url" name="company_website" value="{{ old('company_website') }}" placeholder="https://"></label>
                    <label class="partners-form__wide">Type of Business <small>Optional</small><select name="business_type"><option value="">Select business type</option>@foreach($businessTypes as $value => $label)<option value="{{ $value }}" @selected(old('business_type') === $value)>{{ $label }}</option>@endforeach</select></label>
                    <label class="partners-form__wide">Message / Enquiry *<textarea name="message" rows="6" required placeholder="Dates, guests, budget, preferred accommodation and anything else we should know.">{{ old('message') }}</textarea></label>
                    <label class="partners-form__check partners-form__wide"><input type="checkbox" name="marketing_opt_in" value="1" @checked(old('marketing_opt_in'))><span>Send me occasional Atolliva Maldives B2B offers and partner updates.</span></label>
                </div>
                <button class="partners-button partners-button--submit" type="submit">Send Your Enquiry <span>→</span></button>
            </form>
        </div>
    </section>
</main>


<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('[data-reveal]').forEach((item) => item.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('[data-reveal]').forEach((item) => observer.observe(item));
});
</script>
@endsection
