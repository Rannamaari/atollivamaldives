<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialShareMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_post_response_contains_social_meta_tags(): void
    {
        $post = Post::query()->create([
            'title' => 'Seaplane Tours in Maldives for Families',
            'slug' => 'seaplane-tours-in-maldives-for-families',
            'excerpt' => 'See the Maldives from the sky.',
            'body' => '<p>Guide content.</p>',
            'published' => true,
        ]);

        $response = $this->get(route('blog.show', $post));

        $response->assertOk();
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('rel="canonical"', false);
    }
}
