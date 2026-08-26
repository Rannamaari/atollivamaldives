<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $posts = [
            [
                'title' => 'Seaplane Tours in Maldives',
                'slug' => 'seaplane-tours-in-maldives',
                'category' => 'Things to do',
                'excerpt' => 'See the Maldives from above with scenic seaplane flights over reefs, lagoons, sandbanks, and island resorts.',
                'body' => <<<'HTML'
<p>Seaplane tours in the Maldives offer one of the most memorable views in the Indian Ocean. From the air, the atolls reveal their true shape: rings of reef, turquoise lagoons, tiny sandbanks, and islands edged with white beaches. For many travellers, this is the moment the scale and beauty of the Maldives truly sinks in.</p>
<p>Some guests experience a scenic flight as part of a resort transfer, while others choose dedicated aerial sightseeing as a special part of their holiday. A seaplane experience is especially popular for honeymoons, milestone celebrations, and photography-focused trips because it combines convenience with unforgettable views.</p>
<p>The best time to enjoy a seaplane flight is usually during daylight hours with clear or partly clear skies. Morning and early afternoon often offer beautiful visibility, although exact conditions depend on weather and operations on the day. If you are staying far from Male, a seaplane route can also become part of the adventure rather than simply a transfer.</p>
<p>At Atolliva Maldives, we help travellers understand which islands and itineraries are more likely to include scenic seaplane opportunities, what baggage limitations may apply, and how to build a smooth itinerary when domestic flights or speedboats are also involved. If a seaplane view is high on your Maldives wish list, we can plan your stay around it.</p>
HTML,
                'featured_image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=85',
                'author' => 'Atolliva Maldives',
                'published' => true,
                'featured' => true,
                'published_at' => $now,
                'seo_title' => 'Seaplane Tours in Maldives | Scenic Flights & Travel Tips',
                'seo_description' => 'Discover what to expect from seaplane tours in Maldives, when to go, and how to plan a scenic flight into your island holiday.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Island Hopping in Maldives',
                'slug' => 'island-hopping-in-maldives',
                'category' => 'Things to do',
                'excerpt' => 'Explore local islands, sandbanks, reefs, and different styles of stay with a well-planned island hopping itinerary.',
                'body' => <<<'HTML'
<p>Island hopping in the Maldives is a wonderful way to see more than one side of the country. Instead of staying in a single place, travellers can combine different islands, different atmospheres, and different experiences into one journey. That might mean pairing a peaceful resort with a lively local island, or combining beach time with snorkelling, diving, and cultural stops.</p>
<p>Because the Maldives is spread across a wide chain of atolls, successful island hopping depends on smart route planning. Transfers may involve speedboats, domestic flights, or seaplanes, and not every island combination is practical. The best itineraries balance variety with comfort so that travel days do not become too rushed.</p>
<p>Island hopping can suit couples, families, repeat visitors, and travellers who want a richer picture of the Maldives beyond a single resort stay. Local islands can offer a more grounded glimpse into daily life, while resort islands bring privacy and polished hospitality. Add a snorkelling stop, picnic sandbank, or dolphin cruise, and the trip becomes even more rewarding.</p>
<p>Atolliva Maldives helps design island hopping plans that make logistical sense, match your budget, and still feel relaxed. We can recommend combinations based on your travel style, available transfer schedules, and the experiences you want most, so your itinerary feels seamless rather than complicated.</p>
HTML,
                'featured_image' => 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?auto=format&fit=crop&w=1400&q=85',
                'author' => 'Atolliva Maldives',
                'published' => true,
                'featured' => true,
                'published_at' => $now->copy()->subMinute(),
                'seo_title' => 'Island Hopping in Maldives | How to Plan Multi-Island Trips',
                'seo_description' => 'Learn how island hopping in Maldives works, what transfers to expect, and how to build a smooth multi-island holiday itinerary.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Water Sports and Activities in Maldives',
                'slug' => 'water-sports-and-activities-in-maldives',
                'category' => 'Things to do',
                'excerpt' => 'From paddleboarding and kayaking to jet skiing, snorkelling, and sunset cruises, the Maldives offers water activities for every pace.',
                'body' => <<<'HTML'
<p>The Maldives is made for life on the water. Calm lagoons, clear visibility, and warm seas create the perfect setting for a wide range of activities, whether you prefer gentle exploration or something faster and more adventurous. Many travellers come for the beaches and quickly realise that the most memorable moments often happen just offshore.</p>
<p>Popular water sports in the Maldives include snorkelling, kayaking, stand-up paddleboarding, catamaran sailing, jet skiing, wakeboarding, windsurfing, and parasailing. Depending on where you stay, you may also find sunset cruises, dolphin excursions, fishing trips, and guided reef experiences. Some islands are especially suited to quieter lagoon-based activities, while others are better for broader marine adventures.</p>
<p>The right mix depends on your travel style. Families often enjoy paddleboarding, canoeing, and easy snorkelling. Couples may prefer private cruises or scenic excursions. More active travellers might want motorised sports or a stay with direct access to stronger reef and ocean experiences. Weather, currents, and seasonality can also influence which activities are best during your dates.</p>
<p>At Atolliva Maldives, we help match you with stays that fit the kind of holiday you want. If your trip is centred around water sports and activities in the Maldives, we can recommend islands, resorts, guesthouses, or liveaboards that give you the right balance of relaxation and adventure.</p>
HTML,
                'featured_image' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1400&q=85',
                'author' => 'Atolliva Maldives',
                'published' => true,
                'featured' => false,
                'published_at' => $now->copy()->subMinutes(2),
                'seo_title' => 'Water Sports and Activities in Maldives | Best Experiences at Sea',
                'seo_description' => 'Explore the best water sports and activities in Maldives, from snorkelling and paddleboarding to cruises and jet skiing.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Diving in Maldives',
                'slug' => 'diving-in-maldives',
                'category' => 'Things to do',
                'excerpt' => 'Discover why the Maldives is one of the world’s great dive destinations, with channels, reefs, manta encounters, and liveaboard routes.',
                'body' => <<<'HTML'
<p>Diving in the Maldives is one of the country’s signature experiences and a major reason many travellers return again and again. The appeal lies in variety: colourful coral reefs, pelagic encounters, dramatic channels, cleaning stations, and the possibility of seeing manta rays, reef sharks, eagle rays, turtles, and vast schools of fish in a single trip.</p>
<p>Different atolls offer different strengths. Some are known for beginner-friendly reef diving and resort house reefs, while others are better suited to experienced divers looking for current, depth, and larger marine life. Liveaboards open up even more possibilities by allowing divers to move between prime sites across several atolls rather than being limited to one island base.</p>
<p>The best season for diving in the Maldives depends on the region and the species you hope to see. Visibility, plankton levels, and marine encounters can shift through the year, so it helps to plan with a specific goal in mind. For some guests, the priority is manta rays; for others, it is channel diving, whale sharks, or simply a comfortable first diving holiday with easy access to a dive centre.</p>
<p>Atolliva Maldives can help you choose the right diving holiday based on experience level, budget, and travel style. Whether you want a resort with an excellent dive centre, a guesthouse close to good reefs, or a liveaboard focused on diving routes, we can guide you toward the Maldives experience that fits you best.</p>
HTML,
                'featured_image' => 'https://images.unsplash.com/photo-1544550285-f813152fb2fd?auto=format&fit=crop&w=1400&q=85',
                'author' => 'Atolliva Maldives',
                'published' => true,
                'featured' => true,
                'published_at' => $now->copy()->subMinutes(3),
                'seo_title' => 'Diving in Maldives | Best Atolls, Seasons, and Travel Advice',
                'seo_description' => 'Find out why diving in Maldives is world-famous and how to choose the right atoll, season, resort, or liveaboard for your trip.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->updateOrInsert(
                ['slug' => $post['slug']],
                $post
            );
        }
    }

    public function down(): void
    {
        DB::table('posts')->whereIn('slug', [
            'seaplane-tours-in-maldives',
            'island-hopping-in-maldives',
            'water-sports-and-activities-in-maldives',
            'diving-in-maldives',
        ])->delete();
    }
};
