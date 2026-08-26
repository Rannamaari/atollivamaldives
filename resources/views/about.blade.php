@extends('layouts.app')

@section('title', 'About Atolliva Maldives | Your Maldives, Thoughtfully Planned')
@section('description', 'Learn the story behind Atolliva Maldives, our Maldives-based approach to travel planning, and where our office is located.')

@section('content')
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like to learn more about your travel planning services.'])

<main class="about-page">
    <section class="about-hero">
        <div class="about-hero__backdrop"></div>
        <div class="about-hero__inner">
            <p class="about-hero__kicker">ABOUT ATOLLIVA MALDIVES</p>
            <h1>Your Maldives,<br><em>thoughtfully planned.</em></h1>
            <p class="about-hero__intro">A Maldives travel company built around personal guidance, trusted local perspective, and a more meaningful way to plan island stays, liveaboards, and holiday experiences.</p>
        </div>
    </section>

    <section class="about-story">
        <div class="about-story__inner">
            <div class="about-story__heading">
                <p class="kicker">OUR STORY</p>
                <h2>Created to make the Maldives feel more personal, thoughtful, and reliable.</h2>
            </div>

            <div class="about-story__content">
                <p>Atolliva Maldives was created from a simple idea: to help travellers experience the Maldives in a more personal, thoughtful, and reliable way.</p>

                <p>The name <strong>Atolliva</strong> was inspired by the very thing that makes the Maldives unique — its <strong>atolls</strong>.</p>

                <p>We wanted a name that immediately felt connected to the Maldives, but was also distinctive enough to become its own identity. <strong>“Atoll”</strong> represents our islands, lagoons, reefs and the communities spread across the country. <strong>“Viva”</strong> brings a sense of life, energy and celebration.</p>

                <p>Together, <strong>Atolliva</strong> represents the idea of <strong>bringing the Maldives to life</strong>.</p>

                <p>For us, the Maldives is much more than luxury resorts and beautiful beaches. It is hundreds of islands, local communities, guesthouses, liveaboards, incredible marine life and experiences that can be completely different from one atoll to another.</p>

                <p>That is what Atolliva Maldives aims to connect travellers with.</p>

                <p>Whether you are looking for a luxury resort escape, an affordable local-island holiday, a liveaboard adventure or a carefully planned Maldives package, our goal is to make discovering and planning your trip simple.</p>

                <p class="about-story__signature"><strong>Atolliva Maldives — Your Maldives, thoughtfully planned</strong></p>
            </div>
        </div>
    </section>

    <section class="about-highlights">
        <div class="about-highlights__inner">
            <article class="about-card">
                <p class="kicker">WHY WE EXIST</p>
                <h2>Travel planning with local clarity.</h2>
                <p>We help travellers move beyond generic booking decisions by sharing grounded Maldives knowledge on stays, routes, atolls, transfers, and the kind of experience that suits each trip best.</p>
            </article>

            <article class="about-card">
                <p class="kicker">WHAT WE CONNECT</p>
                <h2>Resorts, guesthouses, liveaboards, and more.</h2>
                <p>From luxury island stays to local-island holidays and ocean adventures, we aim to make the full Maldives feel easier to understand and easier to book with confidence.</p>
            </article>
        </div>
    </section>

    <section class="about-contact">
        <div class="about-contact__inner">
            <div class="about-contact__copy">
                <p class="kicker">OUR OFFICE</p>
                <h2>Visit or contact<br><em>Atolliva Maldives.</em></h2>
                <p>If you would like help planning your trip, discussing travel products, or understanding which Maldives experience is right for you, we are always happy to hear from you.</p>
            </div>

            <div class="about-contact__card">
                <p class="about-contact__label">Office address</p>
                <p class="about-contact__address">{{ $officeAddress }}</p>
                <a class="about-contact__cta" href="{{ route('request-quote') }}">Send an inquiry <span>→</span></a>
            </div>
        </div>
    </section>
</main>

@include('partials.site-footer')
@endsection
