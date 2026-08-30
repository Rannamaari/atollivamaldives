<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_view_printable_quotation(): void
    {
        $user = User::factory()->create();
        $inquiry = Inquiry::create([
            'name' => 'Test Traveller',
            'phone' => '+9609996210',
            'status' => 'new',
            'source' => 'website',
        ]);

        $quotation = Quotation::create([
            'inquiry_id' => $inquiry->id,
            'quotation_date' => now()->toDateString(),
            'customer_name' => 'Test Traveller',
            'currency' => 'USD',
            'nights' => 1,
            'adults' => 2,
            'children' => 0,
            'infants' => 0,
            'chargeable_pax' => 2,
            'items' => [['description' => 'Base room rate', 'qty' => 1, 'unit_price' => 300, 'amount' => 300]],
            'taxes' => [['name' => 'TGST', 'type' => 'percentage_of_subtotal', 'rate' => 17, 'total' => 51]],
            'subtotal' => 300,
            'tax_total' => 51,
            'grand_total' => 351,
        ]);

        $response = $this->actingAs($user)->get(route('quotations.print', $quotation));

        $response->assertOk();
        $response->assertSee('QUOTATION');
        $response->assertSee($quotation->quotation_number);
        $response->assertSee('Test Traveller');
    }
}
