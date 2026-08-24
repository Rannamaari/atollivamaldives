@extends('layouts.app')
@section('title','Maldives stays — Micro Travel')
@section('content')
@include('partials.site-nav', ['whatsAppText' => 'Hello Micro Travel, I would like help planning a Maldives holiday.'])
<section class="listing-page"><p class="kicker">EXPLORE THE MALDIVES</p><h1>Find your <em>stay.</em></h1><form class="filters"><select name="type"><option value="">All stays</option><option value="resort">Resorts</option><option value="guesthouse">Guesthouses</option><option value="liveaboard">Liveaboards</option></select><input name="atoll" value="{{ request('atoll') }}" placeholder="Atoll"><button>FILTER</button></form><div class="cards">@foreach($items as $stay) @php($image=str_starts_with($stay->cover_image,'http')?$stay->cover_image:asset('storage/'.$stay->cover_image))<article><a href="{{ route('accommodations.show',$stay) }}"><div class="photo"><img src="{{ $image }}" alt="{{ $stay->name }}"></div><div class="meta"><span>{{ strtoupper($stay->type->label()) }}</span><span>{{ $stay->island }}</span></div><h3>{{ $stay->name }}</h3><div class="foot"><span>{{ $stay->tagline }}</span><b>FROM {{ $stay->currency }} {{ number_format($stay->price_from) }}</b></div></a></article>@endforeach</div>{{ $items->links() }}</section>
@include('partials.site-footer')
@endsection
