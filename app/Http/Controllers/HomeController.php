<?php
namespace App\Http\Controllers;
use App\Models\Accommodation;
use App\Models\HomePage;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $hero = HomePage::active()->inRandomOrder()->first()
            ?? new HomePage([
                'kicker' => 'YOUR MALDIVES, THOUGHTFULLY PLANNED',
                'heading_line_one' => 'Find your way',
                'heading_line_two' => 'to',
                'heading_emphasis' => 'paradise.',
                'description' => 'Handpicked stays, remarkable ocean journeys, and honest local advice-all in one place.',
            ]);

        return view('home', [
            'featuredStays'=>Accommodation::published()->where('featured',true)->orderBy('sort_order')->take(6)->get(),
            'posts'=>Post::published()->latest('published_at')->take(3)->get(),
            'hero'=>$hero,
        ]);
    }
}
