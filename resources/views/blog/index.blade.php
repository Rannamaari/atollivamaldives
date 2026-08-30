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

    <p class="listing-page__summary">
        Looking for tailored advice instead? <a href="{{ route('request-quote') }}">Send your Maldives trip brief</a> and Atolliva Maldives will help you shortlist suitable options.
    </p>

    {{ $posts->links() }}
</section>

@include('partials.site-footer')
@endsection
