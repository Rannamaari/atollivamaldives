@extends('layouts.app')

@section('content')
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.'])

<section class="listing-page">
    <p class="kicker">THE BLOG</p>
    <h1>Stories from <em>the islands.</em></h1>
    <p class="listing-page__intro">
        Read Maldives travel guides, island ideas, transfer advice, and planning inspiration to help you choose the right resort, guesthouse, liveaboard, city hotel, or package.
    </p>

    <div class="posts">
        @foreach($posts as $post)
            @php($postImage = str_starts_with($post->featured_image ?? '', 'http') ? $post->featured_image : asset('storage/'.$post->featured_image))
            <article>
                <a href="{{ route('blog.show', $post) }}">
                    <img src="{{ $postImage }}" alt="{{ $post->title }} featured image" loading="lazy" decoding="async">
                    <p class="kicker">{{ strtoupper($post->category) }}</p>
                    <h3>{{ $post->title }}</h3>
                    <p>{{ $post->excerpt }}</p>
                    <span>READ POST →</span>
                </a>
            </article>
        @endforeach
    </div>

    <aside class="listing-page__cta" aria-labelledby="blog-cta-title">
        <p class="listing-page__cta-kicker">Thoughtful Planning</p>
        <blockquote class="listing-page__cta-quote">
            <p id="blog-cta-title">
                “Looking for tailored advice instead? Share your Maldives trip brief and we will help you narrow down the right islands, stays, and experiences.”
            </p>
        </blockquote>
        <a class="listing-page__cta-link" href="{{ route('request-quote') }}">
            Start your trip brief <span aria-hidden="true">→</span>
        </a>
    </aside>

    {{ $posts->links() }}
</section>

@include('partials.site-footer')
@endsection
