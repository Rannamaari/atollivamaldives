<?php
namespace App\Http\Controllers;
use App\Models\BlogOffer;
use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View { return view('blog.index',['posts'=>Post::published()->latest('published_at')->paginate(12)]); }
    public function show(Post $post): View {
        abort_unless($post->published,404);

        $offers = BlogOffer::active()->orderBy('sort_order')->get();
        $offer = $offers->isEmpty() ? null : $offers->values()[($post->id - 1) % $offers->count()];

        return view('blog.show', compact('post', 'offer'));
    }
}
