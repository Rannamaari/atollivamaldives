<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestQuotePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_quote_page_loads_with_expected_fields(): void
    {
        config()->set('services.recaptcha.enabled', true);
        config()->set('services.recaptcha.site_key', 'test-site-key');

        $response = $this->get(route('request-quote'));

        $response->assertStatus(200);
        $response->assertSee('REQUEST A QUOTE');
        $response->assertSee('Preferred property type');
        $response->assertSee('Preferred location / distance from Malé airport');
        $response->assertSee('Children ages');
        $response->assertSee('REQUEST QUOTE');
        $response->assertSee('This site is protected by reCAPTCHA', false);
    }
}
