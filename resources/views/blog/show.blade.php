@extends('layouts.app')

@section('content')
@php
    $isArabic = $isArabic ?? false;
    $displayTitle = $isArabic ? $post->arabic_title : $post->title;
    $displayBody = $isArabic ? $post->arabic_body : $post->body;
    $arabicLinkLabel = static function (string $label): string {
        return match ($label) {
            'Browse liveaboards' => 'تصفح رحلات القوارب',
            'See Maldives resorts', 'Browse Maldives resorts' => 'اكتشف منتجعات المالديف',
            'Browse guest houses' => 'تصفح بيوت الضيافة',
            'See Maldives packages' => 'اكتشف باقات المالديف',
            'Explore travel products' => 'استكشف خيارات السفر',
            default => 'اطلب عرضاً مخصصاً',
        };
    };
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

<article class="article{{ $isArabic ? ' article--arabic' : '' }}" @if($isArabic) lang="ar" dir="rtl" @endif>
    <p class="kicker">{{ strtoupper($post->category) }}</p>
    <h1>{{ $displayTitle }}</h1>
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px;">
        <p class="byline" style="margin:0;">{{ $post->author }} · {{ optional($post->published_at)->format('d M Y') }}</p>
        <div class="article__actions">
            @if($isArabic)
                <a class="article-language-switcher" href="{{ route('blog.show', $post) }}" hreflang="en">Read in English</a>
            @elseif($post->hasArabicTranslation())
                <a class="article-language-switcher" href="{{ route('blog.arabic.show', $post) }}" hreflang="ar" lang="ar">العربية <span>Arabic</span></a>
            @endif
            @if(! $isArabic)
                <x-social-share :share="$socialShare" style="compact" />
            @endif
        </div>
    </div>

    @if($post->featured_image)
        <img src="{{ str_starts_with($post->featured_image, 'http') ? $post->featured_image : asset('storage/'.$post->featured_image) }}" alt="{{ $displayTitle }} featured image" decoding="async" fetchpriority="high">
    @endif

    <div class="rich">{!! $displayBody !!}</div>

    <section class="detail-section">
        <h3>{{ $isArabic ? 'خطط لرحلتك إلى المالديف' : 'Plan your Maldives trip next' }}</h3>
        <div class="detail-tags">
            @foreach($travelLinks as $link)
                <a href="{{ $link['url'] }}">{{ $isArabic ? $arabicLinkLabel($link['label']) : $link['label'] }}</a>
            @endforeach
        </div>
    </section>
</article>

@if(! $isArabic && $offer)
    @include('partials.blog-offer', ['offer' => $offer])
@endif

@if(! $isArabic && $relatedPosts->isNotEmpty())
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

@endsection
