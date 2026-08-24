<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $items = [
            [
                'type' => 'liveaboard',
                'name' => 'Ocean Pearl',
                'slug' => 'ocean-pearl',
                'tagline' => 'Private charter for families and friends',
                'summary' => 'A spacious Maldives liveaboard designed for relaxed private charters, sunset dinners and unforgettable time together at sea.',
                'description' => '<p>Ocean Pearl is ideal for travellers who want the privacy of their own yacht-style liveaboard while exploring the Maldives at a comfortable pace. It suits families, couples travelling together and small groups looking for a personalised sea escape.</p>',
                'island' => 'North and South Malé routes',
                'atoll' => 'Kaafu and nearby atolls',
                'address' => 'Departures arranged from Malé or nearby marinas.',
                'price_from' => 6400,
                'currency' => 'USD',
                'price_unit' => 'trip',
                'rating' => 4.8,
                'images' => json_encode(['https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1600&q=85']),
                'amenities' => json_encode(['Private cabins', 'Open-air dining', 'Snorkelling gear', 'Airport transfer']),
                'featured' => 1,
                'published' => 1,
                'sort_order' => 10,
                'seo_title' => null,
                'seo_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'liveaboard',
                'name' => 'Blue Horizon Voyager',
                'slug' => 'blue-horizon-voyager',
                'tagline' => 'Dive routes with comfort and style',
                'summary' => 'A polished liveaboard experience for guests who want rewarding dive days, comfortable cabins and a smooth journey between atolls.',
                'description' => '<p>Blue Horizon Voyager is a strong choice for divers and ocean lovers who want a well-paced itinerary with plenty of time in the water and comfortable spaces to unwind between outings.</p>',
                'island' => 'Central atolls',
                'atoll' => 'Ari, Vaavu and Malé atolls',
                'address' => 'Scheduled embarkation support available from Malé.',
                'price_from' => 3890,
                'currency' => 'USD',
                'price_unit' => 'trip',
                'rating' => 4.7,
                'images' => json_encode(['https://images.unsplash.com/photo-1562281302-809108fd533c?auto=format&fit=crop&w=1600&q=85']),
                'amenities' => json_encode(['Dive deck', 'Full board', 'Air-conditioned cabins', 'Excursion support']),
                'featured' => 1,
                'published' => 1,
                'sort_order' => 20,
                'seo_title' => null,
                'seo_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'liveaboard',
                'name' => 'Coral Wind Charter',
                'slug' => 'coral-wind-charter',
                'tagline' => 'A flexible charter for celebrations at sea',
                'summary' => 'Perfect for birthdays, reunions and private escapes, Coral Wind Charter brings together open decks, island stops and a warm onboard atmosphere.',
                'description' => '<p>Coral Wind Charter is designed for guests who want the experience of living on the sea while exploring sandbanks, reefs and quiet lagoons with their own group.</p>',
                'island' => 'South Malé and Vaavu routes',
                'atoll' => 'Kaafu and Vaavu',
                'address' => 'Private charter departure planning available.',
                'price_from' => 7200,
                'currency' => 'USD',
                'price_unit' => 'trip',
                'rating' => 4.9,
                'images' => json_encode(['https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=85']),
                'amenities' => json_encode(['Private charter', 'Chef onboard', 'Snorkelling stops', 'Sun deck']),
                'featured' => 1,
                'published' => 1,
                'sort_order' => 30,
                'seo_title' => null,
                'seo_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'liveaboard',
                'name' => 'Manta Quest',
                'slug' => 'manta-quest',
                'tagline' => 'For adventurous guests chasing marine encounters',
                'summary' => 'A liveaboard tailored to travellers who want to spend their days exploring channels, reefs and marine life across the Maldives.',
                'description' => '<p>Manta Quest suits active travellers looking for a Maldives journey shaped around the ocean, with practical comfort onboard and strong route flexibility.</p>',
                'island' => 'Northern and central itineraries',
                'atoll' => 'Baa, Raa and central atolls',
                'address' => 'Embarkation details shared at booking stage.',
                'price_from' => 4150,
                'currency' => 'USD',
                'price_unit' => 'trip',
                'rating' => 4.6,
                'images' => json_encode(['https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?auto=format&fit=crop&w=1600&q=85']),
                'amenities' => json_encode(['Guided excursions', 'Marine-focused itineraries', 'Cabin accommodation', 'Full-board dining']),
                'featured' => 0,
                'published' => 1,
                'sort_order' => 40,
                'seo_title' => null,
                'seo_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'liveaboard',
                'name' => 'Sea Story Maldives',
                'slug' => 'sea-story-maldives',
                'tagline' => 'Easygoing luxury on the water',
                'summary' => 'A refined liveaboard stay with elegant social spaces, comfortable cabins and a relaxed pace for guests who want the Maldives from the water.',
                'description' => '<p>Sea Story Maldives is well suited to couples, families and groups who want a polished liveaboard holiday that combines scenic cruising, island time and memorable evenings onboard.</p>',
                'island' => 'Malé, Ari and nearby routes',
                'atoll' => 'Malé and Ari atolls',
                'address' => 'Flexible arrival support for charter guests.',
                'price_from' => 5600,
                'currency' => 'USD',
                'price_unit' => 'trip',
                'rating' => 4.8,
                'images' => json_encode(['https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?auto=format&fit=crop&w=1600&q=85']),
                'amenities' => json_encode(['Premium cabins', 'Lounging deck', 'Water activities', 'Private dining options']),
                'featured' => 0,
                'published' => 1,
                'sort_order' => 50,
                'seo_title' => null,
                'seo_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($items as $item) {
            DB::table('accommodations')->updateOrInsert(
                ['slug' => $item['slug']],
                $item
            );
        }
    }

    public function down(): void
    {
        DB::table('accommodations')->whereIn('slug', [
            'ocean-pearl',
            'blue-horizon-voyager',
            'coral-wind-charter',
            'manta-quest',
            'sea-story-maldives',
        ])->delete();
    }
};
