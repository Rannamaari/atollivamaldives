@extends('layouts.app')
@section('title',$accommodation->seo_title ?: $accommodation->name.' — Micro Travel')
@section('description',$accommodation->seo_description ?: $accommodation->summary)
@section('content')
@php($wa='https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP','9609996210').'?text='.urlencode('Hello Micro Travel, I am interested in '.$accommodation->name.'.'))
@php($galleryImages = collect($accommodation->images ?? [])->filter()->map(fn ($image) => str_starts_with($image, 'http') ? $image : asset('storage/'.$image))->values())
@include('partials.site-nav', ['whatsAppText' => 'Hello Micro Travel, I am interested in '.$accommodation->name.'.'])<article class="detail"><div class="detail-hero"><img src="{{ str_starts_with($accommodation->cover_image,'http')?$accommodation->cover_image:asset('storage/'.$accommodation->cover_image) }}" alt="{{ $accommodation->name }}"></div>
@if($accommodation->type->value === 'liveaboard' && $galleryImages->count() > 1)
<section class="detail-gallery" aria-labelledby="liveaboard-gallery">
    <div class="detail-gallery__head">
        <p class="kicker">LIVEABOARD GALLERY</p>
        <h2 id="liveaboard-gallery">See more of<br><em>{{ $accommodation->name }}</em></h2>
    </div>
    <div class="detail-gallery__grid">
        @foreach($galleryImages as $image)
            <figure class="detail-gallery__item">
                <img src="{{ $image }}" alt="{{ $accommodation->name }} gallery image {{ $loop->iteration }}">
            </figure>
        @endforeach
    </div>
</section>
@endif
<div class="detail-copy"><p class="kicker">{{ strtoupper($accommodation->type->label()) }} · {{ $accommodation->atoll }}</p><h1>{{ $accommodation->name }}</h1><p class="lead">{{ $accommodation->summary }}</p><div class="rich">{!! $accommodation->description !!}</div><h3>Amenities</h3><ul class="amenities">@foreach($accommodation->amenities ?? [] as $amenity)<li>{{ $amenity }}</li>@endforeach</ul><a class="primary" href="{{ $wa }}" target="_blank">ENQUIRE ON WHATSAPP · FROM {{ $accommodation->currency }} {{ number_format($accommodation->price_from) }}</a></div></article>
@include('partials.site-footer')
@endsection
