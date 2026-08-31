<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\SocialShareEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialShareTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_social_share_event_is_stored(): void
    {
        $accommodation = Accommodation::query()->create([
            'type' => 'resort',
            'name' => 'Baros Maldives',
            'slug' => 'baros-maldives',
            'published' => true,
        ]);

        $response = $this->postJson(route('social-share.track'), [
            'content_type' => 'resort',
            'content_id' => $accommodation->id,
            'platform' => 'whatsapp',
            'url' => url('/resorts/baros-maldives?utm_source=whatsapp&utm_medium=social&utm_campaign=share'),
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('social_share_events', [
            'shareable_type' => Accommodation::class,
            'shareable_id' => $accommodation->id,
            'platform' => 'whatsapp',
        ]);
    }

    public function test_invalid_content_type_is_rejected(): void
    {
        $response = $this->postJson(route('social-share.track'), [
            'content_type' => 'supplier',
            'content_id' => 1,
            'platform' => 'whatsapp',
        ]);

        $response->assertStatus(422);
        $this->assertSame(0, SocialShareEvent::query()->count());
    }
}
