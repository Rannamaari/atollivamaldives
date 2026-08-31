<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryAttributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_inquiry_inherits_first_touch_utm_values_from_session(): void
    {
        $this->withSession([
            'marketing_attribution' => [
                'utm_source' => 'whatsapp',
                'utm_medium' => 'social',
                'utm_campaign' => 'share',
                'utm_content' => 'baros-maldives',
                'landing_page' => 'https://atollivamaldives.com/resorts/baros-maldives',
            ],
        ])->post(route('inquiries.store'), [
            'name' => 'Test Customer',
            'phone' => '9609996210',
            'email' => 'test@example.com',
            'arrival_date' => '2026-09-10',
            'departure_date' => '2026-09-12',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Test Customer',
            'utm_source' => 'whatsapp',
            'utm_medium' => 'social',
            'utm_campaign' => 'share',
            'utm_content' => 'baros-maldives',
        ]);

        $this->assertSame('https://atollivamaldives.com/resorts/baros-maldives', Inquiry::query()->first()->landing_page);
    }
}
