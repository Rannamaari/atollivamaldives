@php
    $offerImage = blank($offer->image)
        ? null
        : (str_starts_with($offer->image, 'http') ? $offer->image : asset('storage/'.$offer->image));
@endphp

<section class="blog-offer" aria-labelledby="blog-offer-title">
    @if($offerImage)
        <div class="blog-offer__media">
            <img src="{{ $offerImage }}" alt="{{ $offer->title }}">
        </div>
    @endif

    <div class="blog-offer__content">
        @if($offer->eyebrow)
            <p class="blog-offer__eyebrow">{{ strtoupper($offer->eyebrow) }}</p>
        @endif
        <h2 id="blog-offer-title">{{ $offer->title }}</h2>
        @if($offer->description)
            <p>{{ $offer->description }}</p>
        @endif
        @if($offer->button_url)
            <a class="blog-offer__cta" href="{{ $offer->button_url }}">{{ $offer->button_text }} <span>→</span></a>
        @endif
    </div>
</section>
