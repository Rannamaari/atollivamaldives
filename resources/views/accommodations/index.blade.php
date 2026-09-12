@extends('layouts.app')

@php
    $isArabic = $isArabic ?? app()->getLocale() === 'ar';
    $arabicTypeLabels = ['resort' => 'المنتجعات', 'guesthouse' => 'بيوت الضيافة', 'liveaboard' => 'رحلات القوارب', 'city_hotel' => 'فنادق المدينة', 'package' => 'الباقات'];
    $pageTitle = $isArabic
        ? (($selectedType ? ($arabicTypeLabels[$selectedType->value] ?? $selectedType->label()) : 'خيارات السفر').' — أتوليفا المالديف')
        : ($selectedType ? $selectedType->label().' — Atolliva Maldives' : 'Travel Products — Atolliva Maldives');
    $pageHeading = $selectedIsland?->name
        ?? $selectedAtoll?->name
        ?? ($selectedType ? ($isArabic ? ($arabicTypeLabels[$selectedType->value] ?? $selectedType->label()) : $selectedType->label()) : ($isArabic ? 'خيارات السفر' : 'Travel Products'));
    $pageIntro = $isArabic ? 'اكتشف الإقامات والباقات المختارة بعناية في المالديف، واعثر على الخيار المناسب لرحلتك.' : match (true) {
        (bool) $selectedIsland => 'Explore guest houses in '.$selectedIsland->name.', '.($selectedAtoll?->name ?? 'Maldives').' for your Maldives journey.',
        (bool) $selectedAtoll => 'Explore guest houses across '.$selectedAtoll->name.' for your Maldives journey.',
        (bool) $selectedType => 'Explore handpicked '.$selectedType->label().' for your Maldives journey.',
        default => 'Explore resorts, guest houses, city hotels, packages, and liveaboards across the Maldives.',
    };
    $searchSummary = collect([
        $searchState['destination'] ? 'Destination: '.$searchState['destination'] : null,
        $searchState['check_in'] ? 'Check-in: '.\Carbon\Carbon::parse($searchState['check_in'])->format('d M Y') : null,
        $searchState['check_out'] ? 'Check-out: '.\Carbon\Carbon::parse($searchState['check_out'])->format('d M Y') : null,
        ($searchState['adults'] ?? 0) ? $searchState['adults'].' adults' : null,
        isset($searchState['children']) ? $searchState['children'].' children' : null,
    ])->filter()->implode(' · ');
    $searchAction = match (true) {
        request()->routeIs('resorts.index') => route('resorts.index'),
        request()->routeIs('guesthouses.atoll') && $selectedAtoll => route('guesthouses.atoll', $selectedAtoll),
        request()->routeIs('guesthouses.island') && $selectedAtoll && $selectedIsland => route('guesthouses.island', [$selectedAtoll, $selectedIsland]),
        request()->routeIs('guesthouses.index') => route('guesthouses.index'),
        request()->routeIs('cityhotels.index') => route('cityhotels.index'),
        request()->routeIs('packages.index') => route('packages.index'),
        default => route('accommodations.index'),
    };
@endphp

@section('title', $pageTitle)

@section('content')
@include('partials.site-nav', ['whatsAppText' => 'Hello Atolliva Maldives, I would like help planning a Maldives holiday.'])

<section class="listing-page listing-page--travel">
    <p class="kicker">{{ $isArabic ? 'اكتشف المالديف' : 'EXPLORE THE MALDIVES' }}</p>
    <h1>{{ $pageHeading }}<br><em>{{ $isArabic ? 'مختارة لأجلك.' : 'curated for you.' }}</em></h1>
    <p class="listing-page__intro">{{ $pageIntro }}</p>

    <form class="search-panel" method="get" action="{{ $searchAction }}">
        <label>
            <small>Destination / Property</small>
            <input name="destination" value="{{ $searchState['destination'] }}" placeholder="Resort, island, atoll, Malé...">
        </label>
        <label>
            <small>Check-in</small>
            <input type="date" name="check_in" value="{{ $searchState['check_in'] }}" min="{{ now()->toDateString() }}">
        </label>
        <label>
            <small>Check-out</small>
            <input type="date" name="check_out" value="{{ $searchState['check_out'] }}" min="{{ now()->toDateString() }}">
        </label>
        <label>
            <small>Adults</small>
            <input type="number" name="adults" min="1" value="{{ $searchState['adults'] }}">
        </label>
        <label>
            <small>Children</small>
            <input type="number" name="children" min="0" value="{{ $searchState['children'] }}">
        </label>
        <label>
            <small>Property Type</small>
            <select name="type">
                <option value="">All travel products</option>
                <option value="resort" @selected(request('type') === 'resort' || request()->routeIs('resorts.index'))>Resorts</option>
                <option value="guesthouse" @selected(request('type') === 'guesthouse' || request()->routeIs('guesthouses.index'))>Guest Houses</option>
                <option value="liveaboard" @selected(request('type') === 'liveaboard' || request()->routeIs('liveaboards.index'))>Liveaboards</option>
                <option value="city_hotel" @selected(request('type') === 'city_hotel' || request()->routeIs('cityhotels.index'))>City Hotels</option>
                <option value="package" @selected(request('type') === 'package' || request()->routeIs('packages.index'))>Packages</option>
            </select>
        </label>
        <button type="submit">SEARCH</button>
    </form>

    @if($searchSummary)
        <p class="listing-page__summary">{{ $searchSummary }}</p>
    @endif

    <div class="search-results">
        @forelse($items as $product)
            @php
                $productName = $isArabic && $product->hasArabicTranslation() ? $product->arabic_name : $product->name;
                $productSummary = $isArabic && $product->hasArabicTranslation() ? $product->arabic_summary : $product->summary;
                $image = str_starts_with($product->cover_image, 'http') ? $product->cover_image : asset('storage/'.$product->cover_image);
                $location = collect([$product->islandRelation?->name ?: $product->island, $product->atollRelation?->name ?: $product->atoll, $product->city])->filter()->implode(', ');
                $facilities = $product->facilities->take(4);
                $primaryTransfer = $product->transfers->first();
                $propertyUrl = $product->publicUrl(request()->query());
            @endphp
            <article @class(['search-card', 'search-card--package' => $product->type === \App\Enums\AccommodationType::Package])>
                <a class="search-card__media" href="{{ $propertyUrl }}">
                    <img src="{{ $image }}" alt="{{ $productName }}" loading="lazy" decoding="async">
                    @if($product->type === \App\Enums\AccommodationType::Package && $product->package_best_seller)
                        <span class="package-card-badge">Best Seller</span>
                    @endif
                </a>
                <div class="search-card__content">
                    <div class="search-card__head">
                        <div>
                            <p class="kicker">{{ strtoupper($product->type->label()) }}</p>
                            <h3><a href="{{ $propertyUrl }}">{{ $productName }}</a></h3>
                            @if($location)
                                <p class="search-card__location">{{ $location }}</p>
                            @endif
                        </div>
                        <div class="search-card__pricing">
                            <span>From</span>
                            <strong>{{ $product->currentDisplayCurrency() }} {{ number_format($product->currentDisplayPrice() ?? 0) }}</strong>
                            <small>{{ $product->price_unit === 'trip' ? 'per trip' : ($product->price_unit === 'person' ? 'per person' : 'per night') }}</small>
                        </div>
                    </div>

                    @if($productSummary)
                        <p class="search-card__summary">{{ $productSummary }}</p>
                    @endif

                    @if($product->type === \App\Enums\AccommodationType::Package)
                        <div class="package-offer-meta">
                            @if($product->offer_starts_on || $product->offer_ends_on)
                                <span>
                                    Valid {{ $product->offer_starts_on?->format('d M Y') ?? 'now' }}
                                    @if($product->offer_ends_on) to {{ $product->offer_ends_on->format('d M Y') }} @endif
                                </span>
                            @endif
                            @foreach($product->packageAudienceLabels() as $audience)
                                <span>{{ $audience }}</span>
                            @endforeach
                            @if($product->packageGuestInclusionLabel())
                                <span>{{ $product->packageGuestInclusionLabel() }} included</span>
                            @endif
                        </div>
                    @endif

                    <div class="search-card__meta">
                        @if($product->rating)
                            <span>{{ number_format($product->rating, 1) }} star rating</span>
                        @endif
                        @if($product->published_rooms_count)
                            <span>{{ $product->published_rooms_count }} room types</span>
                        @endif
                        @if($primaryTransfer)
                            <span>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $primaryTransfer->transfer_type)) }} transfer</span>
                        @endif
                    </div>

                    @if($facilities->isNotEmpty())
                        <div class="search-card__facilities">
                            @foreach($facilities as $facility)
                                <span>{{ $facility->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($primaryTransfer)
                        <p class="search-card__transfer">
                            Transfer: {{ $primaryTransfer->name }}
                            @if($primaryTransfer->duration)
                                · {{ $primaryTransfer->duration }}
                            @endif
                        </p>
                    @endif

                    <div class="search-card__actions">
                        <a class="search-card__link" href="{{ $propertyUrl }}">View Property</a>
                        <a class="search-card__button" href="{{ $propertyUrl }}">Request Availability</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="search-empty">
                <h3>{{ $isArabic ? 'لا توجد خيارات مطابقة حالياً.' : 'No matching properties yet.' }}</h3>
                <p>{{ $isArabic ? 'جرّب توسيع وجهتك أو خيارات نوع الإقامة، أو تواصل مع أتوليفا المالديف لنقترح عليك خيارات تناسب رحلتك.' : 'Try broadening your destination or travel product filters, or contact Atolliva Maldives for a custom recommendation.' }}</p>
            </div>
        @endforelse
    </div>

    {{ $items->links() }}
</section>

@include('partials.site-footer')
@endsection
