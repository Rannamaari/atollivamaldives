<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Island;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapAndRobotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_includes_sitemap_and_basic_disallow_rules(): void
    {
        $response = $this->get(route('seo.robots'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Disallow: /travel-products');
        $response->assertSee('Sitemap: '.route('seo.sitemap'));
    }

    public function test_sitemap_xml_lists_public_pages_and_published_content(): void
    {
        $atoll = Atoll::create([
            'name' => 'Kaafu Atoll',
            'slug' => 'kaafu-atoll',
            'status' => 'published',
        ]);

        $island = Island::create([
            'atoll_id' => $atoll->id,
            'name' => 'Maafushi',
            'slug' => 'maafushi',
            'status' => 'published',
        ]);

        $resort = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Baros Maldives',
            'slug' => 'baros-maldives',
            'published' => true,
        ]);

        $guesthouse = Accommodation::create([
            'type' => 'guesthouse',
            'status' => 'published',
            'name' => 'Kaani Grand View',
            'slug' => 'kaani-grand-view',
            'atoll_id' => $atoll->id,
            'island_id' => $island->id,
            'atoll' => 'Kaafu Atoll',
            'island' => 'Maafushi',
            'published' => true,
        ]);

        $post = Post::create([
            'title' => 'Sunrise Sandbank Picnic in Maldives',
            'slug' => 'sunrise-sandbank-picnic-in-maldives',
            'body' => '<p>Guide</p>',
            'published' => true,
        ]);

        $response = $this->get(route('seo.sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('<loc>'.route('home').'</loc>', false);
        $response->assertSee('<loc>'.route('resorts.index').'</loc>', false);
        $response->assertSee('<loc>'.route('guesthouses.atoll', $atoll).'</loc>', false);
        $response->assertSee('<loc>'.route('guesthouses.island', [$atoll, $island]).'</loc>', false);
        $response->assertSee('<loc>'.$resort->publicUrl().'</loc>', false);
        $response->assertSee('<loc>'.$guesthouse->publicUrl().'</loc>', false);
        $response->assertSee('<loc>'.route('blog.show', $post).'</loc>', false);
        $response->assertDontSee('<loc>'.route('request-quote').'</loc>', false);
        $response->assertDontSee('<loc>'.route('accommodations.index').'</loc>', false);
    }

    public function test_request_quote_page_is_marked_noindex(): void
    {
        $response = $this->get(route('request-quote'));

        $response->assertOk();
        $response->assertSee('content="noindex, follow"', false);
    }

    public function test_filtered_listing_page_is_marked_noindex(): void
    {
        $response = $this->get('/resorts?destination=male&check_in=2026-09-10&check_out=2026-09-14&adults=2');

        $response->assertOk();
        $response->assertSee('content="noindex, follow"', false);
    }

    public function test_generic_travel_products_listing_is_marked_noindex(): void
    {
        $response = $this->get(route('accommodations.index'));

        $response->assertOk();
        $response->assertSee('content="noindex, follow"', false);
    }
}
