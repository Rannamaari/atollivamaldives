<?php

namespace Tests\Unit;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\SocialShareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialShareServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_title_override_takes_priority(): void
    {
        SiteSetting::current()->update(['default_share_hashtags' => '#VisitMaldives']);

        $accommodation = Accommodation::query()->create([
            'type' => 'resort',
            'name' => 'Kuramathi Maldives',
            'slug' => 'kuramathi-maldives',
            'published' => true,
            'seo_title' => 'SEO title',
            'social_title' => 'Custom social title',
        ]);

        $share = app(SocialShareService::class)->for($accommodation);

        $this->assertSame('Custom social title', $share->title);
    }

    public function test_post_uses_existing_seo_fallbacks_when_social_fields_are_blank(): void
    {
        $post = Post::query()->create([
            'title' => 'Best Time to Visit the Maldives',
            'slug' => 'best-time-to-visit-the-maldives',
            'body' => '<p>Helpful travel guide body.</p>',
            'published' => true,
            'seo_description' => 'Existing SEO description.',
        ]);

        $share = app(SocialShareService::class)->for($post);

        $this->assertSame('Best Time to Visit the Maldives | Atolliva Maldives', $share->title);
        $this->assertSame('Existing SEO description.', $share->description);
    }

    public function test_tracked_urls_keep_canonical_clean_and_append_utm_values(): void
    {
        $accommodation = Accommodation::query()->create([
            'type' => 'resort',
            'name' => 'Baros Maldives',
            'slug' => 'baros-maldives',
            'published' => true,
        ]);

        $share = app(SocialShareService::class)->for($accommodation)->toArray();
        $decodedFacebookUrl = urldecode($share['facebook_url']);

        $this->assertSame(url('/resorts/baros-maldives'), $share['canonical_url']);
        $this->assertStringContainsString('utm_source=facebook', $decodedFacebookUrl);
        $this->assertStringContainsString('utm_content=baros-maldives', $decodedFacebookUrl);
        $this->assertStringNotContainsString('utm_source', $share['canonical_url']);
    }
}
