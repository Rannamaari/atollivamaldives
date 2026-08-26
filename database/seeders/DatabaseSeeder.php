<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@atollivamaldives.com'], ['name' => 'Atolliva Maldives Admin', 'password' => Hash::make('ChangeMe123!')]);
        $this->call([
            DestinationSeeder::class,
            FacilitySeeder::class,
            MealPlanSeeder::class,
            AccommodationInventorySeeder::class,
        ]);

        foreach ([
            ['title' => 'Which Maldives island is right for you?', 'slug' => 'which-maldives-island-is-right-for-you', 'category' => 'Island guide', 'excerpt' => 'Resort, local island, or liveaboard? Here is how to choose.', 'body' => '<p>The right Maldives island depends on the kind of experience you want. Resorts offer privacy and convenience, local islands bring you closer to Maldivian life, and liveaboards are ideal for divers who want to explore several atolls.</p>', 'featured_image' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1200&q=85', 'published' => true, 'featured' => true, 'published_at' => now(), 'author' => 'Atolliva Maldives'],
            ['title' => 'The best time to visit the Maldives', 'slug' => 'best-time-to-visit-maldives', 'category' => 'Travel tips', 'excerpt' => 'Weather, prices, manta season, and quieter months explained.', 'body' => '<p>The dry northeast monsoon generally offers clearer skies, while the wetter months can bring excellent value and rewarding marine encounters. We help guests balance weather, budget, and the experiences they care about most.</p>', 'featured_image' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=1200&q=85', 'published' => true, 'published_at' => now(), 'author' => 'Atolliva Maldives'],
        ] as $post) {
            Post::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
