<?php

namespace Database\Seeders;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Facility;
use App\Models\Island;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccommodationInventorySeeder extends Seeder
{
    public function run(): void
    {
        $this->demoteLegacyPlaceholders();

        foreach ($this->inventory() as $item) {
            $atoll = filled($item['atoll'] ?? null)
                ? Atoll::query()->where('name', $item['atoll'])->first()
                : null;

            $island = filled($item['island'] ?? null)
                ? Island::query()->where('name', $item['island'])->first()
                : null;

            $attributes = [
                'type' => $item['type'],
                'name' => $item['name'],
                'previous_name' => $item['previous_name'] ?? null,
                'aliases' => $item['aliases'] ?? null,
                'tagline' => $item['tagline'] ?? null,
                'summary' => $item['summary'] ?? null,
                'description' => $item['description'] ?? null,
                'island' => $item['island'] ?? null,
                'island_id' => $island?->id,
                'atoll' => $item['atoll'] ?? null,
                'atoll_id' => $atoll?->id,
                'city' => $item['city'] ?? null,
                'country' => $item['country'] ?? 'Maldives',
                'property_subtype' => $item['property_subtype'] ?? null,
                'featured_image' => $item['featured_image'] ?? $this->placeholderFor($item['type']),
                'images' => $item['images'] ?? [],
                'official_website' => $item['official_website'] ?? null,
                'source_url' => $item['source_url'] ?? null,
                'rating' => $item['rating'] ?? null,
                'airport_distance' => $item['airport_distance'] ?? null,
                'transfer_duration' => $item['transfer_duration'] ?? null,
                'published' => $item['published'] ?? false,
                'featured' => $item['featured'] ?? false,
                'verified' => $item['verified'] ?? false,
                'status' => $item['status'] ?? (($item['published'] ?? false) ? 'published' : 'draft'),
                'seo_title' => $item['seo_title'] ?? null,
                'seo_description' => $item['seo_description'] ?? null,
                'sort_order' => $item['sort_order'] ?? 0,
                'currency' => $item['currency'] ?? 'USD',
                'price_unit' => $item['price_unit'] ?? ($item['type'] === AccommodationType::Liveaboard->value ? 'trip' : 'night'),
                'vessel_name' => $item['vessel_name'] ?? null,
                'vessel_type' => $item['vessel_type'] ?? null,
                'cabins' => $item['cabins'] ?? null,
                'maximum_guests' => $item['maximum_guests'] ?? null,
                'length_meters' => $item['length_meters'] ?? null,
                'cruising_speed_knots' => $item['cruising_speed_knots'] ?? null,
                'diving_available' => $item['diving_available'] ?? null,
                'surfing_available' => $item['surfing_available'] ?? null,
                'snorkeling_available' => $item['snorkeling_available'] ?? null,
                'nitrox_available' => $item['nitrox_available'] ?? null,
                'dhoni_available' => $item['dhoni_available'] ?? null,
                'jacuzzi' => $item['jacuzzi'] ?? null,
                'spa' => $item['spa'] ?? null,
                'restaurant' => $item['restaurant'] ?? null,
                'bar' => $item['bar'] ?? null,
                'wifi' => $item['wifi'] ?? null,
                'departure_port' => $item['departure_port'] ?? null,
                'typical_route' => $item['typical_route'] ?? null,
                'typical_trip_length' => $item['typical_trip_length'] ?? null,
                'minimum_nights' => $item['minimum_nights'] ?? null,
            ];

            $accommodation = Accommodation::updateOrCreate(
                ['slug' => $item['slug']],
                $attributes
            );

            if (! empty($item['facilities'])) {
                $facilityIds = Facility::query()
                    ->whereIn('slug', collect($item['facilities'])->map(fn (string $name) => Str::slug($name)))
                    ->pluck('id')
                    ->all();

                $accommodation->facilities()->sync($facilityIds);
            }
        }
    }

    private function demoteLegacyPlaceholders(): void
    {
        Accommodation::query()
            ->whereIn('slug', [
                'lagoon-water-villa',
                'barefoot-island-stay',
                'ocean-explorer',
                'ocean-pearl',
                'blue-horizon-voyager',
                'coral-wind-charter',
                'manta-quest',
                'sea-story-maldives',
            ])
            ->update([
                'published' => false,
                'featured' => false,
                'verified' => false,
                'status' => 'draft',
            ]);
    }

    private function placeholderFor(string $type): string
    {
        return match ($type) {
            AccommodationType::Resort->value => 'placeholders/resort-placeholder.svg',
            AccommodationType::Guesthouse->value => 'placeholders/guesthouse-placeholder.svg',
            AccommodationType::CityHotel->value => 'placeholders/city-hotel-placeholder.svg',
            AccommodationType::Liveaboard->value => 'placeholders/liveaboard-placeholder.svg',
            default => 'placeholders/resort-placeholder.svg',
        };
    }

    private function inventory(): array
    {
        $resorts = collect([
            'Soneva Fushi',
            'Soneva Jani',
            'Gili Lankanfushi Maldives',
            'Baros Maldives',
            'Kurumba Maldives',
            'Bandos Maldives',
            'Villa Nautica Maldives',
            'Villa Park Maldives',
            'Royal Island Maldives',
            'Meeru Maldives Resort Island',
            'Kuredu Island Resort & Spa',
            'Hurawalhi Island Resort',
            'Komandoo Maldives Island Resort',
            'Kudadoo Maldives Private Island',
            'Anantara Dhigu Maldives Resort',
            'Anantara Veli Maldives Resort',
            'Anantara Kihavah Maldives Villas',
            'Naladhu Private Island Maldives',
            'Conrad Maldives Rangali Island',
            'Waldorf Astoria Maldives Ithaafushi',
            'The St. Regis Maldives Vommuli Resort',
            'W Maldives',
            'Sheraton Maldives Full Moon Resort & Spa',
            'JW Marriott Maldives Resort & Spa',
            'The Ritz-Carlton Maldives, Fari Islands',
            'Patina Maldives, Fari Islands',
            'Hard Rock Hotel Maldives',
            'SAii Lagoon Maldives, Curio Collection by Hilton',
            'OZEN LIFE MAADHOO',
            'OZEN RESERVE BOLIFUSHI',
            'VARU by Atmosphere',
            'Atmosphere Kanifushi Maldives',
            'OBLU SELECT Lobigili',
            'OBLU SELECT Sangeli',
            'OBLU NATURE Helengeli by SENTIDO',
            'Siyam World Maldives',
            'Sun Siyam Iru Fushi',
            'Sun Siyam Olhuveli',
            'Sun Siyam Vilu Reef',
            'Kandima Maldives',
            'Kandolhu Maldives',
            'Amilla Maldives',
            'Dusit Thani Maldives',
            'Alila Kothaifaru Maldives',
            'Cora Cora Maldives',
            'Dhigali Maldives',
            'Furaveri Maldives',
            'Coco Bodu Hithi',
            'Constance Moofushi Maldives',
            'Adaaran Select Hudhuranfushi',
        ])->map(function (string $name, int $index) {
            return [
                'type' => AccommodationType::Resort->value,
                'name' => $name,
                'slug' => Str::slug(Str::replace(',', '', $name)),
                'status' => 'draft',
                'published' => false,
                'verified' => false,
                'featured_image' => $this->placeholderFor(AccommodationType::Resort->value),
                'sort_order' => $index + 1,
            ];
        })->all();

        $resorts = collect($resorts)->map(function (array $item) {
            return match ($item['name']) {
                'Villa Nautica Maldives' => array_replace($item, [
                    'previous_name' => 'Paradise Island Resort & Spa',
                    'aliases' => ['Paradise Island Resort & Spa'],
                    'atoll' => 'Kaafu',
                    'official_website' => 'https://www.paradise-islandmaldives.com/',
                    'source_url' => 'https://www.paradise-islandmaldives.com/',
                    'summary' => 'Villa Nautica Maldives is the current brand identity of the well-known Paradise Island resort in North Malé Atoll.',
                    'published' => true,
                    'verified' => true,
                    'status' => 'published',
                ]),
                'Villa Park Maldives' => array_replace($item, [
                    'previous_name' => 'Sun Island Resort & Spa',
                    'aliases' => ['Sun Island Resort & Spa', 'Villa Park, Sun Island'],
                    'official_website' => 'https://www.villahotels.com/',
                    'summary' => 'Villa Park Maldives is the rebranded identity of the long-established Sun Island resort.',
                ]),
                'Royal Island Maldives' => array_replace($item, [
                    'previous_name' => 'Royal Island Resort & Spa',
                    'aliases' => ['Royal Island Resort & Spa'],
                    'atoll' => 'Baa',
                    'official_website' => 'https://villaresorts.com/royal-island/',
                    'source_url' => 'https://villaresorts.com/royal-island/',
                    'summary' => 'Royal Island Maldives is a beachfront resort in Baa Atoll under the Villa Resorts collection.',
                    'published' => true,
                    'verified' => true,
                    'status' => 'published',
                ]),
                'Soneva Fushi' => array_replace($item, [
                    'atoll' => 'Baa',
                    'summary' => 'Soneva Fushi is a luxury barefoot resort in the Baa Atoll UNESCO Biosphere Reserve.',
                    'official_website' => 'https://soneva.com/resorts/soneva-fushi/',
                    'source_url' => 'https://old.visitmaldives.com/item/soneva-fushi/',
                    'published' => true,
                    'verified' => true,
                    'status' => 'published',
                ]),
                'Soneva Jani' => array_replace($item, [
                    'atoll' => 'Noonu',
                    'summary' => 'Soneva Jani is a luxury Maldives resort in Noonu Atoll known for expansive overwater villas and lagoon experiences.',
                    'official_website' => 'https://soneva.com/resorts/soneva-jani/',
                    'source_url' => 'https://corporate.visitmaldives.com/news/soneva-jani-is-the-first-resort-in-the-maldives-to-achieve-oecm-status/',
                    'published' => true,
                    'verified' => true,
                    'status' => 'published',
                ]),
                default => $item,
            };
        })->all();

        $guesthouses = [
            ['name' => 'Kaani Palm Beach', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Kaani Grand Seaview', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Kaani Village & Spa', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Kaani Beach Hotel', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Arena Beach Hotel', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Triton Prestige Seaview & Spa', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Triton Beach Hotel & Spa', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Velana Beach Hotel', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Crystal Sands', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Stingray Beach Inn', 'island' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Bliss Dhigurah', 'island' => 'Dhigurah', 'atoll' => 'Alif Dhaal'],
            ['name' => 'TME Retreats Dhigurah', 'island' => 'Dhigurah', 'atoll' => 'Alif Dhaal'],
            ['name' => 'White Sand Dhigurah', 'island' => 'Dhigurah', 'atoll' => 'Alif Dhaal'],
            ['name' => 'Dhiguveli Maldives', 'island' => 'Dhigurah', 'atoll' => 'Alif Dhaal'],
            ['name' => 'Athiri Beach Maldives', 'island' => 'Dhigurah', 'atoll' => 'Alif Dhaal'],
            ['name' => 'Season Paradise', 'island' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
            ['name' => 'Samura Maldives Guest House', 'island' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
            ['name' => 'Canopus Retreat Thulusdhoo', 'island' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
            ['name' => 'Reef Edge Thulusdhoo', 'island' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
            ['name' => 'Kahanbu Ocean View', 'island' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
            ['name' => 'West Sands', 'island' => 'Ukulhas', 'atoll' => 'Alif Alif'],
            ['name' => 'Ranthari Hotel & Spa Ukulhas', 'island' => 'Ukulhas', 'atoll' => 'Alif Alif'],
            ['name' => 'SeaLaVie Inn', 'island' => 'Ukulhas', 'atoll' => 'Alif Alif'],
            ['name' => 'Paguro Beach Inn', 'island' => 'Ukulhas', 'atoll' => 'Alif Alif'],
            ['name' => 'Nala Veli Beach & Spa', 'island' => 'Ukulhas', 'atoll' => 'Alif Alif'],
            ['name' => 'Acqua Blu Rasdhoo', 'island' => 'Rasdhoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Rasdhoo Coralville', 'island' => 'Rasdhoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Shallow Lagoon Rasdhoo', 'island' => 'Rasdhoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Palm Residence', 'island' => 'Rasdhoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Rasdhoo Dive Lodge', 'island' => 'Rasdhoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Plumeria Maldives', 'island' => 'Thinadhoo', 'atoll' => 'Vaavu'],
            ['name' => 'Sky Beach Maldives', 'island' => 'Dhiffushi', 'atoll' => 'Kaafu'],
            ['name' => 'Dhiffushi White Sand Beach Hotel', 'island' => 'Dhiffushi', 'atoll' => 'Kaafu'],
            ['name' => 'Thoddoo Retreat', 'island' => 'Thoddoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Samura Panorama Guest House', 'island' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
        ];

        $guesthouses = collect($guesthouses)->map(function (array $item, int $index) {
            return [
                'type' => AccommodationType::Guesthouse->value,
                'slug' => Str::slug($item['name']),
                'country' => 'Maldives',
                'status' => 'draft',
                'published' => false,
                'verified' => false,
                'featured_image' => $this->placeholderFor(AccommodationType::Guesthouse->value),
                'sort_order' => 100 + $index + 1,
            ] + $item;
        })->all();

        $cityHotels = [
            ['name' => 'JEN Maldives Malé by Shangri-La', 'city' => 'Malé', 'island' => 'Malé', 'property_subtype' => 'city_hotel'],
            ['name' => 'Samann Grand', 'city' => 'Malé', 'island' => 'Malé', 'property_subtype' => 'city_hotel'],
            ['name' => 'Maagiri Hotel', 'city' => 'Malé', 'island' => 'Malé', 'property_subtype' => 'city_hotel'],
            ['name' => 'Mookai Hotel', 'city' => 'Malé', 'island' => 'Malé', 'property_subtype' => 'city_hotel'],
            ['name' => 'Hulhule Island Hotel', 'city' => 'Hulhulé', 'island' => 'Hulhulé', 'property_subtype' => 'airport_hotel'],
        ];

        $cityHotels = collect($cityHotels)->map(function (array $item, int $index) {
            return [
                'type' => AccommodationType::CityHotel->value,
                'slug' => Str::slug($item['name']),
                'country' => 'Maldives',
                'status' => 'draft',
                'published' => false,
                'verified' => false,
                'featured_image' => $this->placeholderFor(AccommodationType::CityHotel->value),
                'sort_order' => 200 + $index + 1,
            ] + $item;
        })->all();

        $liveaboards = [
            [
                'name' => 'Scubaspa Ying',
                'slug' => 'scubaspa-ying',
                'summary' => 'Scubaspa Ying is a purpose-built Maldives liveaboard yacht designed for divers, spa travellers, and mixed-interest groups.',
                'official_website' => 'https://scubaspa.com/maldives/',
                'source_url' => 'https://visitmaldives.com/en/liveaboards/scubaspa-ying',
                'published' => true,
                'verified' => true,
                'status' => 'published',
                'vessel_name' => 'Scubaspa Ying',
                'vessel_type' => 'Liveaboard yacht',
                'length_meters' => 50,
                'maximum_guests' => 44,
                'diving_available' => true,
                'snorkeling_available' => true,
                'spa' => true,
                'wifi' => true,
                'jacuzzi' => true,
                'restaurant' => true,
                'bar' => true,
            ],
            ['name' => 'MV Adora'],
            ['name' => 'Carpe Diem'],
            ['name' => 'Carpe Novo'],
            ['name' => 'Carpe Vita'],
            ['name' => 'Emperor Serenity'],
            ['name' => 'Emperor Leo'],
            ['name' => 'Blue Voyager'],
            ['name' => 'Maldives Aggressor II'],
            ['name' => 'Yasawa Princess'],
        ];

        $liveaboards = collect($liveaboards)->map(function (array $item, int $index) {
            return [
                'type' => AccommodationType::Liveaboard->value,
                'slug' => $item['slug'] ?? Str::slug($item['name']),
                'country' => 'Maldives',
                'status' => $item['status'] ?? 'draft',
                'published' => $item['published'] ?? false,
                'verified' => $item['verified'] ?? false,
                'featured_image' => $this->placeholderFor(AccommodationType::Liveaboard->value),
                'sort_order' => 300 + $index + 1,
                'price_unit' => 'trip',
            ] + $item;
        })->all();

        return array_merge($resorts, $guesthouses, $cityHotels, $liveaboards);
    }
}
