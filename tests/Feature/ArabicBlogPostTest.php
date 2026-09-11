<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArabicBlogPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_post_with_arabic_content_links_to_a_searchable_arabic_version(): void
    {
        $post = Post::create([
            'title' => 'Maldives Seaplane Guide',
            'slug' => 'maldives-seaplane-guide',
            'excerpt' => 'An English guide to Maldives seaplane transfers.',
            'body' => '<p>English article.</p>',
            'arabic_title' => 'دليل الطائرة المائية في المالديف',
            'arabic_excerpt' => 'دليل مفيد للانتقالات بالطائرة المائية.',
            'arabic_body' => '<p>مقال عربي.</p>',
            'published' => true,
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee(route('blog.arabic.show', $post), false)
            ->assertSee('hreflang="ar"', false);

        $this->get(route('blog.arabic.show', $post))
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee($post->arabic_title)
            ->assertSee('<link rel="canonical" href="'.route('blog.arabic.show', $post).'">', false)
            ->assertSee('hreflang="en"', false);
    }

    public function test_arabic_url_is_not_available_until_the_translation_is_complete(): void
    {
        $post = Post::create([
            'title' => 'English only post',
            'slug' => 'english-only-post',
            'body' => '<p>English article.</p>',
            'published' => true,
        ]);

        $this->get(route('blog.arabic.show', $post))->assertNotFound();
    }

    public function test_sitemap_lists_complete_arabic_post_translations(): void
    {
        $post = Post::create([
            'title' => 'English post',
            'slug' => 'english-post',
            'body' => '<p>English article.</p>',
            'arabic_title' => 'مقال عربي',
            'arabic_body' => '<p>محتوى عربي.</p>',
            'published' => true,
        ]);

        $this->get(route('seo.sitemap'))
            ->assertOk()
            ->assertSee(route('blog.arabic.show', $post), false);
    }
}
