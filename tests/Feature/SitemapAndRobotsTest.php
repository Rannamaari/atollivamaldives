<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Island;
use App\Models\LiveaboardPage;
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
            'arabic_name' => 'باروس المالديف',
            'arabic_summary' => 'ملخص عربي للمنتجع.',
            'featured_image' => 'accommodations/baros-maldives.webp',
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

        LiveaboardPage::query()->firstOrFail()->update([
            'arabic_title' => 'رحلات القوارب في المالديف',
            'arabic_intro' => 'مقدمة عربية لرحلات القوارب.',
        ]);

        $response = $this->get(route('seo.sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
        $response->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false);
        $response->assertSee('<loc>'.route('home').'</loc>', false);
        $response->assertSee('<loc>'.route('partners.index').'</loc>', false);
        $response->assertSee('<xhtml:link rel="alternate" hreflang="ar" href="'.route('arabic.home').'" />', false);
        $response->assertSee('<loc>'.route('arabic.home').'</loc>', false);
        $response->assertSee('<loc>'.route('arabic.about').'</loc>', false);
        $response->assertSee('<loc>'.route('arabic.faq').'</loc>', false);
        $response->assertSee('<loc>'.route('arabic.resorts.index').'</loc>', false);
        $response->assertSee('<loc>'.route('arabic.liveaboards.index').'</loc>', false);
        $response->assertSee('<loc>'.route('resorts.index').'</loc>', false);
        $response->assertSee('<loc>'.route('guesthouses.atoll', $atoll).'</loc>', false);
        $response->assertSee('<loc>'.route('guesthouses.island', [$atoll, $island]).'</loc>', false);
        $response->assertSee('<loc>'.$resort->publicUrl().'</loc>', false);
        $response->assertSee('<image:image>', false);
        $response->assertSee('<image:title>Baros Maldives</image:title>', false);
        $response->assertSee('<loc>'.url($resort->arabicPublicPath()).'</loc>', false);
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
