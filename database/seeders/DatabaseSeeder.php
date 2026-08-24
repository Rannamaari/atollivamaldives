<?php
namespace Database\Seeders;
use App\Models\Accommodation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email'=>'admin@microtravel.mv'],['name'=>'Micro Travel Admin','password'=>Hash::make('ChangeMe123!')]);
        foreach ([
            ['type'=>'resort','name'=>'Lagoon Water Villa','slug'=>'lagoon-water-villa','tagline'=>'Romantic escape','summary'=>'A private overwater retreat surrounded by the luminous blues of North Malé Atoll.','island'=>'North Malé Atoll','atoll'=>'Kaafu','price_from'=>680,'price_unit'=>'night','images'=>['https://images.unsplash.com/photo-1573843981267-be1999ff37cd?auto=format&fit=crop&w=1400&q=85'],'amenities'=>['Private pool','Breakfast','Airport transfer','House reef'],'featured'=>true,'published'=>true],
            ['type'=>'guesthouse','name'=>'Barefoot Island Stay','slug'=>'barefoot-island-stay','tagline'=>'Local island life','summary'=>'A relaxed guesthouse experience close to Dhigurah’s long white beach.','island'=>'Dhigurah','atoll'=>'Alif Dhaal','price_from'=>95,'price_unit'=>'night','images'=>['https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=85'],'amenities'=>['Breakfast','Wi-Fi','Excursions','Beach access'],'featured'=>true,'published'=>true],
            ['type'=>'liveaboard','name'=>'Ocean Explorer','slug'=>'ocean-explorer','tagline'=>'7 nights · 15 dives','summary'=>'A week-long liveaboard route through the Maldives’ most rewarding dive sites.','island'=>'Central Atolls','atoll'=>'Multiple atolls','price_from'=>1890,'price_unit'=>'trip','images'=>['https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1400&q=85'],'amenities'=>['Full board','Nitrox','Dive guide','Airport transfer'],'featured'=>true,'published'=>true],
        ] as $item) Accommodation::updateOrCreate(['slug'=>$item['slug']],$item);

        foreach ([
            ['title'=>'Which Maldives island is right for you?','slug'=>'which-maldives-island-is-right-for-you','category'=>'Island guide','excerpt'=>'Resort, local island, or liveaboard? Here is how to choose.','body'=>'<p>The right Maldives island depends on the kind of experience you want. Resorts offer privacy and convenience, local islands bring you closer to Maldivian life, and liveaboards are ideal for divers who want to explore several atolls.</p>','featured_image'=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1200&q=85','published'=>true,'featured'=>true,'published_at'=>now()],
            ['title'=>'The best time to visit the Maldives','slug'=>'best-time-to-visit-maldives','category'=>'Travel tips','excerpt'=>'Weather, prices, manta season, and quieter months explained.','body'=>'<p>The dry northeast monsoon generally offers clearer skies, while the wetter months can bring excellent value and rewarding marine encounters. We help guests balance weather, budget, and the experiences they care about most.</p>','featured_image'=>'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=1200&q=85','published'=>true,'published_at'=>now()],
        ] as $post) Post::updateOrCreate(['slug'=>$post['slug']],$post);
    }
}
