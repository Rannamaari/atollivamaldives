<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_resort_page_uses_canonical_metadata_and_breadcrumb_schema(): void
    {
        $resort = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Kuramathi Maldives',
            'slug' => 'kuramathi-maldives',
            'summary' => 'A beautifully located Maldives resort.',
            'published' => true,
        ]);

        $response = $this->get(route('resorts.show', $resort));

        $response->assertOk();
        $response->assertSee('<title>Kuramathi Maldives | Rates &amp; Holiday Packages | Atolliva Maldives</title>', false);
        $response->assertSee('<link rel="canonical" href="'.route('resorts.show', $resort).'">', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"name":"Resorts"', false);
        $response->assertSee('"@type":"LodgingBusiness"', false);
    }

    public function test_blog_post_uses_article_schema_and_canonical_metadata(): void
    {
        $post = Post::create([
            'title' => 'Maldives Family Packages With Speedboat Transfers',
            'slug' => 'maldives-family-packages-speedboat-transfer',
            'excerpt' => 'Helpful guide to family stays and transfers.',
            'body' => '<p>Helpful guide content.</p>',
            'published' => true,
        ]);

        $response = $this->get(route('blog.show', $post));

        $response->assertOk();
        $response->assertSee('<title>Maldives Family Packages With Speedboat Transfers | Atolliva Maldives</title>', false);
        $response->assertSee('<link rel="canonical" href="'.route('blog.show', $post).'">', false);
        $response->assertSee('"@type":"Article"', false);
        $response->assertSee('"name":"Blog"', false);
    }
}
