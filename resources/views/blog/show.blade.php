@extends('layouts.app')

@section('content')
@php
    $category = strtolower((string) $post->category);
    $travelLinks = match (true) {
        str_contains($category, 'liveaboard'), str_contains($category, 'diving') => [
            ['label' => 'Browse liveaboards', 'url' => route('liveaboards.index')],
            ['label' => 'See Maldives resorts', 'url' => route('resorts.index')],
            ['label' => 'Request a custom quote', 'url' => route('request-quote')],
        ],
        str_contains($category, 'guest'), str_contains($category, 'island') => [
            ['label' => 'Browse guest houses', 'url' => route('guesthouses.index')],
            ['label' => 'See Maldives packages', 'url' => route('packages.index')],
            ['label' => 'Request a custom quote', 'url' => route('request-quote')],
        ],
        default => [
            ['label' => 'Browse Maldives resorts', 'url' => route('resorts.index')],
            ['label' => 'Explore travel products', 'url' => route('accommodations.index')],
            ['label' => 'Request a custom quote', 'url' => route('request-quote')],
        ],
    };
@endphp

@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.'])

<article class="article">
    <p class="kicker">{{ strtoupper($post->category) }}</p>
    <h1>{{ $post->title }}</h1>
    <p class="byline">{{ $post->author }} · {{ optional($post->published_at)->format('d M Y') }}</p>

    @if($post->featured_image)
        <img src="{{ str_starts_with($post->featured_image, 'http') ? $post->featured_image : asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }} featured image" decoding="async" fetchpriority="high">
    @endif

    <div class="rich">{!! $post->body !!}</div>

    <section class="detail-section">
        <h3>Plan your Maldives trip next</h3>
        <div class="detail-tags">
            @foreach($travelLinks as $link)
                <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
            @endforeach
        </div>
    </section>
</article>

@if($offer)
    @include('partials.blog-offer', ['offer' => $offer])
@endif

@if($relatedPosts->isNotEmpty())
    <section class="journal">
        <div class="section-title">
            <div>
                <p class="kicker">KEEP EXPLORING</p>
                <h2>More Maldives<br><em>travel guides.</em></h2>
            </div>
            <a class="under" href="{{ route('blog.index') }}">VIEW ALL POSTS →</a>
        </div>
        <div class="posts">
            @foreach($relatedPosts as $relatedPost)
                @php($relatedImage = str_starts_with($relatedPost->featured_image ?? '', 'http') ? $relatedPost->featured_image : asset('storage/'.$relatedPost->featured_image))
                <article>
                    <a href="{{ route('blog.show', $relatedPost) }}">
                        <img src="{{ $relatedImage }}" alt="{{ $relatedPost->title }} featured image" loading="lazy" decoding="async">
                        <p class="kicker">{{ strtoupper($relatedPost->category) }}</p>
                        <h3>{{ $relatedPost->title }}</h3>
                        <p>{{ $relatedPost->excerpt }}</p>
                        <span>READ POST →</span>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@include('partials.site-footer')
@endsection
