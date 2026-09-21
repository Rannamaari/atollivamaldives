<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_exposes_complete_travel_agency_schema(): void
    {
        SiteSetting::current()->update([
            'business_address' => 'M. Ithaamuiyge 1, Alimasmagu',
            'business_address_locality' => 'Male City',
            'business_address_country_code' => 'MV',
            'business_opening_days' => ['Monday', 'Sunday'],
            'business_opening_time' => '09:00',
            'business_closing_time' => '18:00',
            'facebook_url' => 'https://www.facebook.com/atollivamaldives',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('"@type":"TravelAgency"', false);
        $response->assertSee('"@id":"'.url('/').'#travel-agency"', false);
        $response->assertSee('"streetAddress":"M. Ithaamuiyge 1, Alimasmagu"', false);
        $response->assertSee('"addressLocality":"Male City"', false);
        $response->assertSee('"@type":"OpeningHoursSpecification"', false);
        $response->assertSee('"hasOfferCatalog"', false);
        $response->assertSee('https://www.facebook.com/atollivamaldives', false);
        $response->assertSee('https://x.com/myatolliva', false);
    }

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
