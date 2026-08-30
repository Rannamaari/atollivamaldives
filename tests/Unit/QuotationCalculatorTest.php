<?php

namespace Tests\Unit;

use App\Services\QuotationCalculator;
use Tests\TestCase;

class QuotationCalculatorTest extends TestCase
{
    public function test_it_calculates_room_tax_example_correctly(): void
    {
        $result = app(QuotationCalculator::class)->prepare([
            'nights' => 1,
            'adults' => 2,
            'children' => 0,
            'chargeable_pax' => 2,
            'items' => [
                ['description' => 'Base room rate', 'qty' => 1, 'unit_price' => 300],
            ],
            'taxes' => [
                ['name' => 'TGST', 'type' => 'percentage_of_subtotal', 'rate' => 17],
                ['name' => 'Service Charge', 'type' => 'percentage_of_subtotal', 'rate' => 10],
                ['name' => 'Green Tax', 'type' => 'per_person_per_night', 'rate' => 12],
            ],
        ]);

        $this->assertSame(300.0, $result['subtotal']);
        $this->assertSame(105.0, $result['tax_total']);
        $this->assertSame(405.0, $result['grand_total']);
    }
}
