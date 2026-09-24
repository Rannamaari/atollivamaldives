@extends('layouts.app')

@section('content')
@php($isArabic = $isArabic ?? app()->getLocale() === 'ar')
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.'])

<section class="listing-page">
    <p class="kicker">{{ $isArabic ? 'المدونة' : 'THE BLOG' }}</p>
    <h1>{!! $isArabic ? 'حكايات من <em>الجزر.</em>' : 'Stories from <em>the islands.</em>' !!}</h1>
    <p class="listing-page__intro">
        {{ $isArabic ? 'اقرأ أدلة السفر إلى المالديف وأفكار الجزر ونصائح التنقل وإلهام التخطيط لمساعدتك على اختيار المنتجع أو بيت الضيافة أو رحلة القارب أو فندق المدينة أو الباقة المناسبة.' : 'Read Maldives travel guides, island ideas, transfer advice, and planning inspiration to help you choose the right resort, guesthouse, liveaboard, city hotel, or package.' }}
    </p>

    <div class="posts">
        @foreach($posts as $post)
            @php($postImage = str_starts_with($post->featured_image ?? '', 'http') ? $post->featured_image : asset('storage/'.$post->featured_image))
            <article>
                <a href="{{ $isArabic && $post->hasArabicTranslation() ? route('blog.arabic.show', $post) : route('blog.show', $post) }}">
                    <img src="{{ $postImage }}" alt="{{ $isArabic && $post->hasArabicTranslation() ? $post->arabic_title : $post->title }} featured image" loading="lazy" decoding="async">
                    <p class="kicker">{{ strtoupper($post->category) }}</p>
                    <h3>{{ $isArabic && $post->hasArabicTranslation() ? $post->arabic_title : $post->title }}</h3>
                    <p>{{ $isArabic && $post->hasArabicTranslation() ? $post->arabic_excerpt : $post->excerpt }}</p>
                    <span>{{ $isArabic ? ($post->hasArabicTranslation() ? 'اقرأ المقال ←' : 'اقرأ بالإنجليزية ←') : 'READ POST →' }}</span>
                </a>
            </article>
        @endforeach
    </div>

    <aside class="listing-page__cta" aria-labelledby="blog-cta-title">
        <p class="listing-page__cta-kicker">{{ $isArabic ? 'تخطيط مدروس' : 'Thoughtful Planning' }}</p>
        <blockquote class="listing-page__cta-quote">
            <p id="blog-cta-title">
                {{ $isArabic ? '“هل تبحث عن نصيحة تناسب رحلتك؟ شاركنا تفاصيل رحلتك إلى المالديف وسنساعدك على اختيار الجزر والإقامات والتجارب المناسبة.”' : '“Looking for tailored advice instead? Share your Maldives trip brief and we will help you narrow down the right islands, stays, and experiences.”' }}
            </p>
        </blockquote>
        <a class="listing-page__cta-link" href="{{ route('request-quote') }}">
            {{ $isArabic ? 'ابدأ تفاصيل رحلتك' : 'Start your trip brief' }} <span aria-hidden="true">{{ $isArabic ? '←' : '→' }}</span>
        </a>
    </aside>

    {{ $posts->links() }}
</section>

@endsection
