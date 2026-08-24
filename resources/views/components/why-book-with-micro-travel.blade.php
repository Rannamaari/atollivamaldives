@php
    $benefits = [
        [
            'icon' => 'heroicon-o-map-pin',
            'title' => 'WE ARE MALDIVES-BASED',
            'description' => 'As a local travel agency, Micro Travel offers firsthand knowledge and dependable assistance throughout your journey. We are based in the Maldives and are always within easy reach whenever you need support.',
        ],
        [
            'icon' => 'heroicon-o-building-office-2',
            'title' => 'CAREFULLY SELECTED STAYS',
            'description' => 'From luxurious island resorts and charming guesthouses to unforgettable liveaboard experiences, we carefully select accommodation options to match your preferences, expectations and budget.',
        ],
        [
            'icon' => 'heroicon-o-chat-bubble-left-right',
            'title' => 'LOCAL KNOWLEDGE, PERSONAL SERVICE',
            'description' => 'Our team understands the Maldives and can provide practical, honest recommendations on accommodation, transfers, dining, excursions, diving, snorkelling and other island experiences.',
        ],
        [
            'icon' => 'heroicon-o-tag',
            'title' => 'COMPETITIVE RATES & SPECIAL OFFERS',
            'description' => 'We work closely with our travel partners to bring you attractive rates, seasonal promotions and exclusive benefits-helping you enjoy the best possible value for your Maldives holiday.',
        ],
        [
            'icon' => 'heroicon-o-paper-airplane',
            'title' => 'INTERNATIONAL FLIGHT ASSISTANCE',
            'description' => 'We can help you find suitable international flights and convenient connections to and from the Maldives, making your entire journey easier to plan.',
        ],
        [
            'icon' => 'heroicon-o-shield-check',
            'title' => 'TRAVEL INSURANCE ASSISTANCE',
            'description' => 'Protect your journey against unexpected medical expenses, trip disruptions, lost luggage, missing travel documents and other travel-related emergencies with suitable travel insurance options.',
        ],
        [
            'icon' => 'heroicon-o-heart',
            'title' => 'YOUR JOURNEY, PERSONALLY PLANNED',
            'description' => 'Every traveller is different. Whether you are planning a honeymoon, family holiday, diving adventure or luxury escape, Micro Travel will help you create a Maldives experience designed around you.',
            'featured' => true,
        ],
    ];
@endphp

<section aria-labelledby="why-book-with-micro-travel" class="why-book">
    <div class="why-book__inner">
        <div class="why-book__header">
            <h2 id="why-book-with-micro-travel" class="why-book__heading">
                WHY BOOK WITH MICRO TRAVEL?
            </h2>
        </div>

        <div class="why-book__grid">
            @foreach ($benefits as $benefit)
                <article @class([
                    'why-book__item',
                    'why-book__item--featured' => !empty($benefit['featured']),
                ])>
                    <div class="why-book__icon" aria-hidden="true">
                        @svg($benefit['icon'], 'why-book__icon-svg')
                    </div>

                    <h3 class="why-book__title">
                        {{ $benefit['title'] }}
                    </h3>

                    <p class="why-book__description">
                        {{ $benefit['description'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>
