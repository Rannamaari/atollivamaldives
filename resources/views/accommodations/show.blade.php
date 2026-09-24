@extends('layouts.app')

@section('content')
@php
    $isArabic = $isArabic ?? app()->getLocale() === 'ar';
    if ($isArabic && $accommodation->hasArabicTranslation()) {
        // Keep relations, pricing, and route data intact while presenting the
        // reviewed Arabic fields on the Arabic URL.
        $accommodation = clone $accommodation;
        $accommodation->name = $accommodation->arabic_name ?: $accommodation->name;
        $accommodation->tagline = $accommodation->arabic_tagline ?: $accommodation->tagline;
        $accommodation->summary = $accommodation->arabic_summary ?: $accommodation->summary;
        $accommodation->description = $accommodation->arabic_description ?: $accommodation->description;
    }
    $copy = $isArabic ? [
        'gallery' => 'معرض الصور', 'gallery_title' => 'شاهد المزيد من', 'package_offer' => 'الباقة الحالية', 'best_seller' => 'الأكثر طلباً',
        'per_person' => 'للشخص', 'per_night' => 'لليلة', 'per_package' => 'للباقة', 'included' => 'مشمول في هذه الباقة', 'travel_period' => 'فترة السفر',
        'available_now' => 'متاحة الآن', 'to' => 'إلى', 'available_to' => 'متاحة لـ', 'rating' => 'تقييم نجوم', 'check_in' => 'تسجيل الوصول',
        'check_out' => 'تسجيل المغادرة', 'from_airport' => 'من المطار', 'whatsapp' => 'تحقق من التوفر عبر واتساب', 'send_inquiry' => 'أرسل استفساراً',
        'overview' => 'نظرة عامة', 'facilities' => 'المرافق', 'room_types' => 'أنواع الغرف', 'from' => 'ابتداءً من', 'up_to' => 'حتى',
        'guests' => 'ضيوف', 'transfers' => 'الانتقالات', 'house_rules' => 'قواعد الإقامة', 'cancellation' => 'سياسة الإلغاء',
        'request_kicker' => 'اطلب السعر والتوفر', 'request_title' => 'أخبرنا عن<br><em>خطط سفرك.</em>',
        'request_copy' => 'شاركنا تواريخك وتفضيلاتك لـ :property، وسيتحقق فريقنا من التوفر وأحدث الأسعار المناسبة لك.',
        'name' => 'الاسم', 'phone' => 'رقم واتساب', 'email' => 'البريد الإلكتروني', 'nationality' => 'الجنسية', 'select_country' => 'اختر الدولة',
        'travel_type' => 'نوع الرحلة', 'arrival' => 'تاريخ الوصول', 'departure' => 'تاريخ المغادرة', 'adults' => 'البالغون', 'children' => 'الأطفال',
        'infants' => 'الرضع', 'budget' => 'الميزانية التقريبية', 'room' => 'الغرفة المفضلة', 'meal_plan' => 'خطة الوجبات',
        'special_requests' => 'طلبات خاصة', 'budget_placeholder' => 'مثال: 3,000 دولار أمريكي', 'room_placeholder' => 'مثال: فيلا فوق الماء', 'meal_placeholder' => 'مثال: إقامة مع نصف إقامة',
        'message_placeholder' => "العقار: :property\nأي طلبات خاصة...", 'submit' => 'اطلب السعر ←',
        'similar_kicker' => 'قد تعجبك أيضاً', 'similar_title' => 'خيارات<br><em>مشابهة.</em>', 'similar_copy' => 'خيارات أخرى في المالديف اخترناها للمسافرين المهتمين بـ :property.',
        'guides_kicker' => 'أدلة السفر إلى المالديف', 'guides_title' => 'قراءة مفيدة<br><em>قبل الحجز.</em>', 'visit_blog' => 'زيارة المدونة ←', 'read_post' => 'اقرأ المقال ←',
        'international' => 'الضيوف الدوليون', 'locals' => 'المقيمون في المالديف', 'expats' => 'حاملو تصريح العمل',
    ] : [
        'gallery' => 'GALLERY', 'gallery_title' => 'See more of', 'package_offer' => 'CURRENT PACKAGE OFFER', 'best_seller' => 'Best Seller',
        'per_person' => 'per person', 'per_night' => 'per night', 'per_package' => 'per package', 'included' => 'included in this offer', 'travel_period' => 'Travel period',
        'available_now' => 'Available now', 'to' => 'to', 'available_to' => 'Available to', 'rating' => 'star rating', 'check_in' => 'Check-in',
        'check_out' => 'Check-out', 'from_airport' => 'from airport', 'whatsapp' => 'CHECK AVAILABILITY ON WHATSAPP', 'send_inquiry' => 'SEND INQUIRY',
        'overview' => 'Overview', 'facilities' => 'Facilities', 'room_types' => 'Room Types', 'from' => 'From', 'up_to' => 'Up to',
        'guests' => 'guests', 'transfers' => 'Transfers', 'house_rules' => 'House Rules', 'cancellation' => 'Cancellation Policy',
        'request_kicker' => 'REQUEST PRICE & AVAILABILITY', 'request_title' => 'Tell us your<br><em>travel plans.</em>',
        'request_copy' => 'Share your dates and preferences for :property, and our team will check availability and the latest selling rates for you.',
        'name' => 'Your name', 'phone' => 'WhatsApp number', 'email' => 'Email address', 'nationality' => 'Nationality', 'select_country' => 'Select country',
        'travel_type' => 'Travel type', 'arrival' => 'Arrival date', 'departure' => 'Departure date', 'adults' => 'Adults', 'children' => 'Children',
        'infants' => 'Infants', 'budget' => 'Approximate budget', 'room' => 'Preferred room', 'meal_plan' => 'Meal plan',
        'special_requests' => 'Special requests', 'budget_placeholder' => 'e.g. USD 3,000', 'room_placeholder' => 'e.g. Water Villa', 'meal_placeholder' => 'e.g. Half Board',
        'message_placeholder' => "Property: :property\nAny special requests...", 'submit' => 'REQUEST PRICE →',
        'similar_kicker' => 'YOU MAY ALSO LIKE', 'similar_title' => 'Similar<br><em>properties.</em>', 'similar_copy' => 'More Maldives options selected for travellers considering :property.',
        'guides_kicker' => 'MALDIVES TRAVEL GUIDES', 'guides_title' => 'Helpful reading<br><em>before you book.</em>', 'visit_blog' => 'VISIT THE BLOG →', 'read_post' => 'READ POST →',
        'international' => 'International guests', 'locals' => 'Maldives locals', 'expats' => 'Work permit holders',
    ];
    $typeLabels = $isArabic ? ['resort' => 'منتجع', 'guesthouse' => 'بيت ضيافة', 'liveaboard' => 'رحلة قارب', 'city_hotel' => 'فندق مدينة', 'package' => 'باقة'] : [];
    $typeLabel = $typeLabels[$accommodation->type->value] ?? $accommodation->type->label();
    $waMessage = $isArabic
        ? "مرحباً أتوليفا المالديف،\n\nأرغب في التحقق من التوفر لـ:\n\nالعقار: {$accommodation->name}"
        : "Hello Atolliva Maldives,\n\nI would like to check availability for:\n\nProperty: {$accommodation->name}";
    $wa = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP', '9609996210').'?text='.urlencode($waMessage);
    $galleryImages = $accommodation->galleryImages->pluck('image_path')->filter()->map(fn ($image) => str_starts_with($image, 'http') ? $image : asset('storage/'.$image))->values();
    if ($galleryImages->isEmpty()) {
        $galleryImages = collect($accommodation->images ?? [])->filter()->map(fn ($image) => str_starts_with($image, 'http') ? $image : asset('storage/'.$image))->values();
    }
    $location = collect([$accommodation->islandRelation?->name ?: $accommodation->island, $accommodation->atollRelation?->name ?: $accommodation->atoll, $accommodation->city])->filter()->implode(', ');
    $searchArrivalDate = request('check_in');
    $searchDepartureDate = request('check_out');
    $searchAdults = max(1, (int) request('adults', 2));
    $searchChildren = max(0, (int) request('children', 0));
@endphp

@include('partials.site-nav', ['whatsAppText' => $isArabic ? 'مرحباً أتوليفا المالديف، أنا مهتم بـ '.$accommodation->name.'.' : 'Hello Atolliva Maldives, I am interested in '.$accommodation->name.'.'])

<article class="detail detail--travel">
    <div class="detail-hero">
        <img src="{{ str_starts_with($accommodation->cover_image, 'http') ? $accommodation->cover_image : asset('storage/'.$accommodation->cover_image) }}" alt="{{ $accommodation->name }} {{ $isArabic ? 'في المالديف' : 'in the Maldives' }}" decoding="async" fetchpriority="high">
    </div>

    @if($galleryImages->count() > 1)
        <section class="detail-gallery" aria-labelledby="property-gallery">
            <div class="detail-gallery__head">
                <p class="kicker">{{ strtoupper($typeLabel) }} {{ $copy['gallery'] }}</p>
                <h2 id="property-gallery">{{ $copy['gallery_title'] }}<br><em>{{ $accommodation->name }}</em></h2>
            </div>
            <div class="detail-gallery__grid">
                @foreach($galleryImages as $image)
                    <figure class="detail-gallery__item">
                        <img src="{{ $image }}" alt="{{ $accommodation->name }} {{ $copy['gallery'] }} {{ $loop->iteration }}" loading="lazy" decoding="async">
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    <div class="detail-copy">
        <p class="kicker">{{ strtoupper($typeLabel) }} @if($location) · {{ $location }} @endif</p>
        <h1>{{ $accommodation->name }}</h1>
        @if($accommodation->summary)
            <p class="lead">{{ $accommodation->summary }}</p>
        @endif

        @if($accommodation->type === \App\Enums\AccommodationType::Package)
            @php
                $activePricePeriod = $accommodation->activePackagePricePeriod();
            @endphp
            <section class="package-offer" aria-label="Package offer details">
                <div>
                    <p class="kicker">{{ $copy['package_offer'] }}</p>
                    @if($accommodation->package_best_seller)
                        <span class="package-card-badge package-card-badge--inline">{{ $copy['best_seller'] }}</span>
                    @endif
                    <strong>{{ $accommodation->currentDisplayCurrency() }} {{ number_format($accommodation->currentDisplayPrice() ?? 0) }}</strong>
                    <span>{{ $accommodation->price_unit === 'person' ? $copy['per_person'] : ($accommodation->price_unit === 'night' ? $copy['per_night'] : $copy['per_package']) }}</span>
                    @if($accommodation->packageGuestInclusionLabel())
                        <p class="package-offer__inclusion">{{ $accommodation->packageGuestInclusionLabel() }} {{ $copy['included'] }}</p>
                    @endif
                    @if(data_get($activePricePeriod, 'label'))
                        <p>{{ data_get($activePricePeriod, 'label') }}</p>
                    @endif
                </div>
                <div class="package-offer__details">
                    @if($accommodation->offer_starts_on || $accommodation->offer_ends_on)
                        <p><b>{{ $copy['travel_period'] }}</b><span>{{ $accommodation->offer_starts_on?->format('d M Y') ?? $copy['available_now'] }}@if($accommodation->offer_ends_on) {{ $copy['to'] }} {{ $accommodation->offer_ends_on->format('d M Y') }}@endif</span></p>
                    @endif
                    @if($accommodation->packageAudienceLabels())
                        <p><b>{{ $copy['available_to'] }}</b><span>{{ collect($accommodation->eligible_audiences ?? [])->map(fn ($audience) => $copy[$audience] ?? $audience)->implode(' · ') }}</span></p>
                    @endif
                </div>
            </section>
        @endif

        <div class="detail-summary-bar">
            @if($accommodation->rating)
                <span>{{ number_format($accommodation->rating, 1) }} {{ $copy['rating'] }}</span>
            @endif
            @if($accommodation->check_in_time)
                <span>{{ $copy['check_in'] }} {{ $accommodation->check_in_time->format('H:i') }}</span>
            @endif
            @if($accommodation->check_out_time)
                <span>{{ $copy['check_out'] }} {{ $accommodation->check_out_time->format('H:i') }}</span>
            @endif
            @if($accommodation->airport_distance)
                <span>{{ $accommodation->airport_distance }} {{ $copy['from_airport'] }}</span>
            @endif
        </div>

        <div class="detail-actions">
            <a class="primary" href="{{ $wa }}" target="_blank">{{ $copy['whatsapp'] }}</a>
            <a class="outline-button" href="{{ route('inquiries.store') }}" onclick="event.preventDefault(); document.getElementById('property-inquiry').scrollIntoView({ behavior: 'smooth' });">{{ $copy['send_inquiry'] }}</a>
            @unless($isArabic)
                <x-social-share :share="$socialShare" />
            @endunless
        </div>

        @if($accommodation->description)
            <section class="detail-section">
                <h3>{{ $copy['overview'] }}</h3>
                <div class="rich">{!! $accommodation->description !!}</div>
            </section>
        @endif

        @if($accommodation->facilities->isNotEmpty())
            <section class="detail-section">
                <h3>{{ $copy['facilities'] }}</h3>
                <div class="detail-tags">
                    @foreach($accommodation->facilities as $facility)
                        <span>{{ $facility->name }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        @if($accommodation->rooms->isNotEmpty())
            <section class="detail-section">
                <h3>{{ $copy['room_types'] }}</h3>
                <div class="room-grid">
                    @foreach($accommodation->rooms as $room)
                        @php
                            $roomImage = optional($room->images->first())->image_path;
                            $roomImage = $roomImage ? (str_starts_with($roomImage, 'http') ? $roomImage : asset('storage/'.$roomImage)) : (str_starts_with($accommodation->cover_image, 'http') ? $accommodation->cover_image : asset('storage/'.$accommodation->cover_image));
                        @endphp
                        <article class="room-card">
                            <img src="{{ $roomImage }}" alt="{{ $room->name }} {{ $isArabic ? 'في' : 'at' }} {{ $accommodation->name }}" loading="lazy" decoding="async">
                            <div class="room-card__body">
                                <div class="room-card__head">
                                    <h4>{{ $room->name }}</h4>
                                    @if($room->base_price)
                                        <p>{{ $copy['from'] }} {{ $room->currency }} {{ number_format($room->base_price) }}</p>
                                    @endif
                                </div>
                                @if($room->short_description)
                                    <p>{{ $room->short_description }}</p>
                                @endif
                                <div class="room-card__meta">
                                    @if($room->room_size)<span>{{ $room->room_size }}</span>@endif
                                    @if($room->bed_type)<span>{{ $room->bed_type }}</span>@endif
                                    @if($room->max_occupancy)<span>{{ $copy['up_to'] }} {{ $room->max_occupancy }} {{ $copy['guests'] }}</span>@endif
                                </div>
                                @if($room->facilities->isNotEmpty())
                                    <div class="detail-tags detail-tags--compact">
                                        @foreach($room->facilities->take(5) as $facility)
                                            <span>{{ $facility->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if($accommodation->transfers->isNotEmpty())
            <section class="detail-section">
                <h3>{{ $copy['transfers'] }}</h3>
                <div class="transfer-list">
                    @foreach($accommodation->transfers as $transfer)
                        <article class="transfer-card">
                            <div>
                                <h4>{{ $transfer->name }}</h4>
                                <p>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $transfer->transfer_type)) }}@if($transfer->duration) · {{ $transfer->duration }}@endif</p>
                            </div>
                            @if($transfer->adult_price)
                                <strong>{{ $transfer->currency }} {{ number_format($transfer->adult_price) }}</strong>
                            @endif
                        </article>
                    @endforeach
                </div>
                @if($accommodation->transfer_notes)
                    <p class="detail-note">{{ $accommodation->transfer_notes }}</p>
                @endif
            </section>
        @endif

        @if($accommodation->house_rules || $accommodation->cancellation_policy)
            <section class="detail-section detail-section--two-col">
                @if($accommodation->house_rules)
                    <div>
                        <h3>{{ $copy['house_rules'] }}</h3>
                        <div class="rich">{!! $accommodation->house_rules !!}</div>
                    </div>
                @endif
                @if($accommodation->cancellation_policy)
                    <div>
                        <h3>{{ $copy['cancellation'] }}</h3>
                        <div class="rich">{!! $accommodation->cancellation_policy !!}</div>
                    </div>
                @endif
            </section>
        @endif
    </div>
</article>

<section class="enquiry enquiry--property" id="property-inquiry">
    <div>
        <p class="kicker">{{ $copy['request_kicker'] }}</p>
        <h2>{!! $copy['request_title'] !!}</h2>
        <p>{{ str_replace(':property', $accommodation->name, $copy['request_copy']) }}</p>
    </div>
    <form method="post" action="{{ route('inquiries.store') }}" data-recaptcha-form>
        @csrf
        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif
        @error('form')
            <p class="success" style="background:#fff3f0;color:#8a2f1e;">{{ $message }}</p>
        @enderror
        @include('partials.recaptcha-fields', ['action' => 'property_inquiry_submit'])
        <div class="form-grid">
            <label>{{ $copy['name'] }}<input name="name" required value="{{ old('name') }}"></label>
            <label>{{ $copy['phone'] }}<input name="phone" required value="{{ old('phone') }}"></label>
            <label>{{ $copy['email'] }}<input type="email" name="email" value="{{ old('email') }}"></label>
            <label>{{ $copy['nationality'] }}<select name="nationality"><option value="">{{ $copy['select_country'] }}</option>@foreach(config('countries.all', []) as $country)<option value="{{ $country }}" @selected(old('nationality') === $country)>{{ $country }}</option>@endforeach</select></label>
            <input type="hidden" name="accommodation_id" value="{{ $accommodation->id }}">
            <label>{{ $copy['travel_type'] }}<select name="travel_type"><option value="{{ $accommodation->type->value }}" selected>{{ $typeLabel }}</option></select></label>
            <label>{{ $copy['arrival'] }}<input type="date" name="arrival_date" value="{{ old('arrival_date', $searchArrivalDate) }}" min="{{ now()->toDateString() }}"></label>
            <label>{{ $copy['departure'] }}<input type="date" name="departure_date" value="{{ old('departure_date', $searchDepartureDate) }}" min="{{ now()->toDateString() }}"></label>
            <label>{{ $copy['adults'] }}<input type="number" name="adults" min="1" value="{{ old('adults', old('travellers', $searchAdults)) }}"></label>
            <label>{{ $copy['children'] }}<input type="number" name="children" min="0" value="{{ old('children', $searchChildren) }}"></label>
            <label>{{ $copy['infants'] }}<input type="number" name="infants" min="0" value="{{ old('infants', 0) }}"></label>
            <label>{{ $copy['budget'] }}<input name="budget" placeholder="{{ $copy['budget_placeholder'] }}" value="{{ old('budget') }}"></label>
            <label>{{ $copy['room'] }}<input name="preferred_room" value="{{ old('preferred_room') }}" placeholder="{{ $copy['room_placeholder'] }}"></label>
            <label>{{ $copy['meal_plan'] }}<input name="meal_plan" value="{{ old('meal_plan') }}" placeholder="{{ $copy['meal_placeholder'] }}"></label>
            <label class="wide">{{ $copy['special_requests'] }}<textarea name="message" rows="4" placeholder="{{ str_replace(':property', $accommodation->name, $copy['message_placeholder']) }}">{{ old('message', ($isArabic ? 'العقار: ' : 'Property: ').$accommodation->name) }}</textarea></label>
        </div>
        <button>{{ $copy['submit'] }}</button>
    </form>
</section>

@if($similarProperties->isNotEmpty())
    <section class="stays stays--similar">
        <div class="section-title">
            <div>
                <p class="kicker">{{ $copy['similar_kicker'] }}</p>
                <h2>{!! $copy['similar_title'] !!}</h2>
            </div>
            <p>{{ str_replace(':property', $accommodation->name, $copy['similar_copy']) }}</p>
        </div>
        <div class="cards">
            @foreach($similarProperties as $property)
                @php
                    $image = str_starts_with($property->cover_image, 'http') ? $property->cover_image : asset('storage/'.$property->cover_image);
                    $propertyName = $isArabic && $property->hasArabicTranslation() ? $property->arabic_name : $property->name;
                    $propertyTagline = $isArabic && $property->hasArabicTranslation() ? $property->arabic_tagline : $property->tagline;
                @endphp
                <article>
                    <a href="{{ $property->publicUrl() }}">
                        <div class="photo"><img src="{{ $image }}" alt="{{ $propertyName }} {{ $isArabic ? 'في المالديف' : 'in the Maldives' }}" loading="lazy" decoding="async"></div>
                        <div class="meta"><span>{{ strtoupper($typeLabels[$property->type->value] ?? $property->type->label()) }}</span><span>{{ $property->island }}</span></div>
                        <h3>{{ $propertyName }}</h3>
                        <div class="foot"><span>{{ $propertyTagline }}</span><b>{{ $copy['from'] }} {{ $property->currentDisplayCurrency() }} {{ number_format($property->currentDisplayPrice() ?? 0) }}</b></div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@if($relatedPosts->isNotEmpty())
    <section class="journal">
        <div class="section-title">
            <div>
                <p class="kicker">{{ $copy['guides_kicker'] }}</p>
                <h2>{!! $copy['guides_title'] !!}</h2>
            </div>
            <a class="under" href="{{ route($isArabic ? 'arabic.blog.index' : 'blog.index') }}">{{ $copy['visit_blog'] }}</a>
        </div>
        <div class="posts">
            @foreach($relatedPosts as $post)
                @php
                    $postImage = str_starts_with($post->featured_image ?? '', 'http') ? $post->featured_image : asset('storage/'.$post->featured_image);
                    $postIsArabic = $isArabic && $post->hasArabicTranslation();
                    $postUrl = $postIsArabic ? route('blog.arabic.show', $post) : route('blog.show', $post);
                @endphp
                <article>
                    <a href="{{ $postUrl }}">
                        <img src="{{ $postImage }}" alt="{{ $postIsArabic ? $post->arabic_title : $post->title }} {{ $isArabic ? 'صورة المقال' : 'featured image' }}" loading="lazy" decoding="async">
                        <p class="kicker">{{ strtoupper($post->category) }}</p>
                        <h3>{{ $postIsArabic ? $post->arabic_title : $post->title }}</h3>
                        <p>{{ $postIsArabic ? $post->arabic_excerpt : $post->excerpt }}</p>
                        <span>{{ $copy['read_post'] }}</span>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@endsection
