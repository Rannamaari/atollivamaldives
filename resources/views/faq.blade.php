@extends('layouts.app')
@section('title','Maldives FAQ | Atolliva Maldives Travel Questions')
@section('description','Find answers to common Maldives travel questions about transfers, weather, visas, resorts, liveaboards, budgets, honeymoons, and planning with Atolliva Maldives.')
@section('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqSchema,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection
@section('content')
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning my Maldives holiday.'])

<main class="faq-page">
    <section class="faq-hero">
        <div class="faq-hero__backdrop"></div>
        <div class="faq-hero__inner">
            <p class="faq-hero__kicker">MALDIVES FAQ</p>
            <h1>Frequently asked<br><em>questions.</em></h1>
            <p class="faq-hero__intro">Everything travellers ask most often about the Maldives, from airports and transfers to resorts, weather, liveaboards, and planning your holiday with Atolliva Maldives.</p>
        </div>
    </section>

    <section class="faq-summary">
        <div class="faq-summary__inner">
            <div>
                <p class="kicker">QUICK GUIDE</p>
                <h2>Helpful answers,<br><em>all in one place.</em></h2>
            </div>
            <p>Use this page to explore the essentials before you book. It is designed to help both first-time visitors and returning travellers understand how Maldives holidays work and what Atolliva Maldives can arrange for you.</p>
        </div>
    </section>

    <section class="faq-content">
        <div class="faq-content__inner">
            <aside class="faq-index" aria-label="FAQ sections">
                <p class="faq-index__title">On this page</p>
                @foreach($sections as $section)
                    <a href="#{{ \Illuminate\Support\Str::slug($section['title']) }}">{{ $section['title'] }}</a>
                @endforeach
            </aside>

            <div class="faq-groups">
                @foreach($sections as $section)
                    <section class="faq-group" id="{{ \Illuminate\Support\Str::slug($section['title']) }}">
                        <div class="faq-group__head">
                            <p class="kicker">{{ strtoupper($loop->iteration === 1 ? 'Plan your trip' : 'Travel with Atolliva') }}</p>
                            <h2>{{ $section['title'] }}</h2>
                            <p>{{ $section['intro'] }}</p>
                        </div>

                        <div class="faq-items">
                            @foreach($section['items'] as $item)
                                <details class="faq-item" @if($loop->first && $loop->parent->first) open @endif>
                                    <summary>{{ $item['question'] }}</summary>
                                    <div class="faq-item__answer">
                                        @foreach($item['answer'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>

    <section class="faq-cta">
        <div class="faq-cta__inner">
            <p class="faq-cta__kicker">NEED PERSONALISED ADVICE?</p>
            <h2>Tell us what kind of<br><em>Maldives holiday</em> you want.</h2>
            <p>Whether you are comparing resorts, planning a honeymoon, arranging transfers, or choosing between a guesthouse and liveaboard, Atolliva Maldives can help you narrow it down.</p>
            <a href="https://wa.me/{{ env('MICRO_TRAVEL_WHATSAPP','9609996210') }}?text={{ urlencode('Hello Atolliva Maldives, I would like help planning my Maldives holiday.') }}" target="_blank">ASK ON WHATSAPP <span>↗</span></a>
        </div>
    </section>
</main>

@include('partials.site-footer')
@endsection
