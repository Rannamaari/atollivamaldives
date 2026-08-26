<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Swimming Pool', 'category' => 'Leisure'],
            ['name' => 'Private Pool', 'category' => 'Leisure'],
            ['name' => 'Spa', 'category' => 'Wellness'],
            ['name' => 'Gym', 'category' => 'Wellness'],
            ['name' => 'Kids Club', 'category' => 'Family'],
            ['name' => 'Diving', 'category' => 'Activities'],
            ['name' => 'Snorkeling', 'category' => 'Activities'],
            ['name' => 'Water Sports', 'category' => 'Activities'],
            ['name' => 'Surfing', 'category' => 'Activities'],
            ['name' => 'House Reef', 'category' => 'Marine'],
            ['name' => 'Restaurant', 'category' => 'Dining'],
            ['name' => 'Bar', 'category' => 'Dining'],
            ['name' => 'Wi-Fi', 'category' => 'Convenience'],
            ['name' => 'Airport Transfer', 'category' => 'Convenience'],
            ['name' => 'Family Friendly', 'category' => 'Family'],
            ['name' => 'Honeymoon', 'category' => 'Occasions'],
            ['name' => 'Wedding', 'category' => 'Occasions'],
            ['name' => 'Butler Service', 'category' => 'Service'],
            ['name' => 'Room Service', 'category' => 'Service'],
            ['name' => 'Laundry', 'category' => 'Service'],
            ['name' => 'Excursions', 'category' => 'Activities'],
            ['name' => 'Fishing', 'category' => 'Activities'],
            ['name' => 'Dolphin Cruise', 'category' => 'Activities'],
            ['name' => 'Manta Excursions', 'category' => 'Activities'],
            ['name' => 'Whale Shark Excursions', 'category' => 'Activities'],
            ['name' => 'Bicycle', 'category' => 'Activities'],
            ['name' => 'Tennis', 'category' => 'Activities'],
            ['name' => 'Yoga', 'category' => 'Wellness'],
            ['name' => 'Bikini Beach', 'category' => 'Beach'],
            ['name' => 'Beachfront', 'category' => 'Beach'],
            ['name' => 'Nitrox', 'category' => 'Diving'],
            ['name' => 'Dhoni Support', 'category' => 'Diving'],
            ['name' => 'Jacuzzi', 'category' => 'Leisure'],
        ] as $facility) {
            Facility::updateOrCreate(
                ['slug' => Str::slug($facility['name'])],
                $facility + ['icon' => null]
            );
        }
    }
}
