@php
    $whatsAppNumber = env('MICRO_TRAVEL_WHATSAPP', '9609996210');
    $whatsAppUrl = 'https://wa.me/'.$whatsAppNumber.'?text='.urlencode('Hello Atolliva Maldives, I would like help planning a Maldives holiday.');
    $contactEmail = env('MICRO_TRAVEL_CONTACT_EMAIL', 'admin@atollivamaldives.com');
    $socialLinks = [
        ['label' => 'Facebook', 'href' => '#', 'icon' => 'facebook'],
        ['label' => 'X', 'href' => '#', 'icon' => 'x'],
        ['label' => 'Instagram', 'href' => '#', 'icon' => 'instagram'],
        ['label' => 'TikTok', 'href' => '#', 'icon' => 'tiktok'],
    ];
@endphp

<footer class="site-footer" aria-labelledby="site-footer-title">
    <div class="site-footer__shell">
        <div class="site-footer__brand">
            <div class="site-footer__brand-lockup">
                @include('partials.logo', ['dark' => true])
                <p id="site-footer-title" class="site-footer__tagline">Your Maldives, Thoughtfully Planned</p>
            </div>

            <p class="site-footer__summary">
                Atolliva Maldives helps travellers discover the right island experience with calm local guidance and carefully selected stays.
                From luxury resorts to liveaboards and tailored packages, we make planning feel personal from the very first message.
            </p>

            <a class="site-footer__cta" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener" aria-label="Start planning on WhatsApp">
                <span class="site-footer__cta-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M12 3.2A8.8 8.8 0 0 0 4.5 16.7L3 21l4.5-1.4A8.8 8.8 0 1 0 12 3.2Zm0 15.9c-1.3 0-2.5-.3-3.5-1l-.3-.2-2.7.8.9-2.6-.2-.3a7 7 0 1 1 5.8 3.3Zm3.9-5.2c-.2-.1-1.1-.6-1.3-.6-.2-.1-.3-.1-.5.1l-.4.6c-.1.1-.2.2-.4.1a5.8 5.8 0 0 1-1.7-1 6.4 6.4 0 0 1-1.2-1.5c-.1-.2 0-.3.1-.4l.3-.4.2-.4v-.4l-.6-1.4c-.2-.4-.3-.4-.5-.4h-.4a.8.8 0 0 0-.6.3c-.2.2-.8.8-.8 1.9 0 1 .8 2.1.9 2.2.1.2 1.6 2.6 4 3.5 2.3.9 2.3.6 2.8.6.4-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1-.1-.1-.3-.1-.5-.2Z" fill="currentColor"/>
                    </svg>
                </span>
                <span>Start planning on WhatsApp ↗</span>
            </a>

            <div class="site-footer__socials" aria-label="Social media links">
                @foreach($socialLinks as $social)
                    <a class="site-footer__social-link" href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                        @switch($social['icon'])
                            @case('facebook')
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M13.5 21v-7h2.3l.4-2.8h-2.7V9.4c0-.8.2-1.4 1.4-1.4H16V5.5c-.2 0-.9-.1-1.8-.1-2.5 0-4.2 1.5-4.2 4.3v1.5H7.5V14H10v7h3.5Z" fill="currentColor"/>
                                </svg>
                            @break
                            @case('x')
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M17.8 3H21l-7 8 8.2 10h-6.4l-5-6.1L5.5 21H2.3l7.5-8.5L2 3h6.5l4.5 5.6L17.8 3Zm-1.1 16h1.8L7.5 4.9H5.6L16.7 19Z" fill="currentColor"/>
                                </svg>
                            @break
                            @case('instagram')
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M7.3 3h9.4A4.3 4.3 0 0 1 21 7.3v9.4a4.3 4.3 0 0 1-4.3 4.3H7.3A4.3 4.3 0 0 1 3 16.7V7.3A4.3 4.3 0 0 1 7.3 3Zm0 1.8A2.5 2.5 0 0 0 4.8 7.3v9.4a2.5 2.5 0 0 0 2.5 2.5h9.4a2.5 2.5 0 0 0 2.5-2.5V7.3a2.5 2.5 0 0 0-2.5-2.5H7.3Zm9.8 1.4a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8A3.2 3.2 0 1 0 12 15.2 3.2 3.2 0 0 0 12 8.8Z" fill="currentColor"/>
                                </svg>
                            @break
                            @case('tiktok')
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M14.6 3c.3 2 1.5 3.6 3.4 4.5 1 .5 2 .7 3 .7V11c-1.4 0-2.8-.3-4.1-.9v5.8c0 3-2.4 5.4-5.4 5.4A5.5 5.5 0 0 1 6 15.9a5.5 5.5 0 0 1 7-5.2v2.9a2.7 2.7 0 0 0-1-.2c-1.5 0-2.7 1.2-2.7 2.7s1.2 2.7 2.7 2.7 2.7-1.2 2.7-2.7V3h2.6Z" fill="currentColor"/>
                                </svg>
                            @break
                        @endswitch
                    </a>
                @endforeach
            </div>
        </div>

        <div class="site-footer__group">
            <p class="site-footer__heading">Travel Products</p>
            <a href="{{ route('resorts.index') }}">Resorts</a>
            <a href="{{ route('guesthouses.index') }}">Guest Houses</a>
            <a href="{{ route('liveaboards.index') }}">Liveaboards</a>
            <a href="{{ route('cityhotels.index') }}">City Hotels</a>
            <a href="{{ route('packages.index') }}">Packages</a>
        </div>

        <div class="site-footer__group">
            <p class="site-footer__heading">Explore</p>
            <a href="{{ route('accommodations.index') }}">All Travel Products</a>
            <a href="{{ route('request-quote') }}">Request Quote</a>
            <a href="{{ route('blog.index') }}">Blog</a>
            <a href="{{ route('faq') }}">FAQ</a>
            <a href="{{ route('home') }}#about">About Us</a>
            <a href="{{ route('home') }}#experiences">Experiences</a>
        </div>

        <div class="site-footer__group">
            <p class="site-footer__heading">Discover</p>
            <a href="{{ route('blog.show', 'seaplane-tours-in-maldives') }}">Seaplane Tours</a>
            <a href="{{ route('blog.show', 'island-hopping-in-maldives') }}">Island Hopping</a>
            <a href="{{ route('blog.show', 'water-sports-and-activities-in-maldives') }}">Water Sports &amp; Activities</a>
            <a href="{{ route('blog.show', 'diving-in-maldives') }}">Diving</a>
        </div>

        <div class="site-footer__contact-card">
            <p class="site-footer__heading">Direct Assistance</p>
            <p class="site-footer__contact-copy">Need help choosing the right island, route, or package? Speak with us directly for quick planning support.</p>
            <div class="site-footer__contact-list">
                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">WhatsApp: +{{ $whatsAppNumber }}</a>
                <p>Daily assistance: 9:00 AM to 10:00 PM MVT</p>
            </div>
        </div>
    </div>

    <div class="site-footer__bar">
        <small>© 2026 Atolliva Maldives. All rights reserved.</small>
        <div class="site-footer__legal">
            <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
            <a href="{{ url('/terms-of-service') }}">Terms of Service</a>
            <a href="{{ url('/cookie-settings') }}">Cookie Settings</a>
        </div>
    </div>
</footer>
