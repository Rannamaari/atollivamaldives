<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_page_is_public_and_contains_registration_form(): void
    {
        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee('You bring the traveller.')
            ->assertSee('Give us')
            ->assertSee('one enquiry.')
            ->assertSee(route('partners.store'), false);
    }

    public function test_travel_professional_can_submit_a_partner_application(): void
    {
        config()->set('services.recaptcha.enabled', false);

        $response = $this->post(route('partners.store'), [
            'company' => 'Ocean Routes Travel',
            'contact_name' => 'Amina Saleem',
            'country' => 'United Arab Emirates',
            'email' => 'amina@example.com',
            'phone' => '+971 50 123 4567',
            'company_website' => 'https://example.com',
            'markets' => 'UAE and Saudi Arabia',
            'message' => 'We arrange premium Indian Ocean holidays.',
            'marketing_opt_in' => '1',
        ]);

        $response->assertRedirect(route('partners.index').'#partner-form');
        $response->assertSessionHas('partner_success');
        $this->assertDatabaseHas('partner_applications', [
            'status' => 'new',
            'company' => 'Ocean Routes Travel',
            'email' => 'amina@example.com',
            'website' => 'https://example.com',
            'business_type' => null,
            'estimated_enquiries' => null,
            'marketing_opt_in' => true,
        ]);
    }

    public function test_partner_application_rejects_an_invalid_business_type(): void
    {
        config()->set('services.recaptcha.enabled', false);

        $this->post(route('partners.store'), [
            'company' => 'Invalid Agency',
            'contact_name' => 'Test Person',
            'country' => 'Maldives',
            'email' => 'hello@example.com',
            'phone' => '+960 999 6210',
            'business_type' => 'invalid',
            'estimated_enquiries' => 'occasional',
            'message' => 'Please quote this request.',
        ])->assertSessionHasErrors('business_type');

        $this->assertDatabaseCount('partner_applications', 0);
    }
}
