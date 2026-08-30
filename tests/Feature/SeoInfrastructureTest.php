<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\SeoRedirect;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_uses_site_setting_defaults_for_meta_tags(): void
    {
        SiteSetting::current()->update([
            'site_name' => 'Atolliva Maldives',
            'default_meta_title' => 'Custom SEO Title',
            'default_meta_description' => 'Custom SEO Description',
            'default_robots_index' => false,
            'default_robots_follow' => true,
            'google_search_console_verification' => 'verify-token',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>Maldives Travel Agency | Resorts, Guesthouses &amp; Holiday Packages | Atolliva Maldives</title>', false);
        $response->assertSee('content="Discover Maldives resorts, guesthouses, liveaboards, honeymoon escapes and holiday packages with Atolliva Maldives, your Maldives travel agency for thoughtfully planned journeys."', false);
        $response->assertSee('content="noindex, follow"', false);
        $response->assertSee('content="verify-token"', false);
    }

    public function test_redirect_fallback_uses_stored_redirects(): void
    {
        SeoRedirect::create([
            'source_path' => '/old-page',
            'destination_path' => '/new-page',
            'http_status' => 301,
            'active' => true,
        ]);

        $response = $this->get('/old-page');

        $response->assertRedirect('/new-page');
        $this->assertSame(1, SeoRedirect::first()->fresh()->hits);
    }

    public function test_accommodation_slug_changes_create_redirect_record(): void
    {
        $accommodation = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Sample Resort',
            'slug' => 'sample-resort',
            'published' => true,
        ]);

        $accommodation->update(['slug' => 'sample-resort-new']);

        $this->assertDatabaseHas('seo_redirects', [
            'source_path' => '/resorts/sample-resort',
            'destination_path' => '/resorts/sample-resort-new',
            'http_status' => 301,
        ]);
    }

    public function test_post_slug_changes_create_redirect_record(): void
    {
        $post = Post::create([
            'title' => 'Sample Post',
            'slug' => 'sample-post',
            'body' => '<p>Body</p>',
            'published' => true,
        ]);

        $post->update(['slug' => 'sample-post-updated']);

        $this->assertDatabaseHas('seo_redirects', [
            'source_path' => '/blog/sample-post',
            'destination_path' => '/blog/sample-post-updated',
            'http_status' => 301,
        ]);
    }
}
