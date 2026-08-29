<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InquiryCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_inquiry_can_be_submitted_when_recaptcha_is_disabled(): void
    {
        config()->set('services.recaptcha.enabled', false);
        config()->set('services.recaptcha.site_key', null);
        config()->set('services.recaptcha.secret_key', null);

        $response = $this->from(route('request-quote'))->post(route('inquiries.store'), [
            'name' => 'Test Traveller',
            'phone' => '+9609996210',
            'travel_type' => 'resort',
            'source' => 'request_quote',
        ]);

        $response->assertRedirect(route('request-quote'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', [
            'name' => 'Test Traveller',
            'phone' => '+9609996210',
            'travel_type' => 'resort',
        ]);
    }

    public function test_inquiry_requires_a_valid_recaptcha_token_when_enabled(): void
    {
        config()->set('services.recaptcha.enabled', true);
        config()->set('services.recaptcha.site_key', 'site-key');
        config()->set('services.recaptcha.secret_key', 'secret-key');
        config()->set('services.recaptcha.min_score', 0.5);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action' => 'request_quote_submit',
                'score' => 0.1,
            ]),
        ]);

        $response = $this->from(route('request-quote'))->post(route('inquiries.store'), [
            'name' => 'Test Traveller',
            'phone' => '+9609996210',
            'travel_type' => 'resort',
            'source' => 'request_quote',
            'recaptcha_token' => 'test-token',
            'recaptcha_action' => 'request_quote_submit',
        ]);

        $response->assertRedirect(route('request-quote'));
        $response->assertSessionHasErrors('form');
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_honeypot_field_blocks_bot_like_submissions(): void
    {
        config()->set('services.recaptcha.enabled', false);

        $response = $this->from(route('request-quote'))->post(route('inquiries.store'), [
            'name' => 'Spam Bot',
            'phone' => '+9609996210',
            'website' => 'https://spam.example',
        ]);

        $response->assertRedirect(route('request-quote'));
        $response->assertSessionHasErrors('website');
        $this->assertDatabaseCount('inquiries', 0);
    }
}
