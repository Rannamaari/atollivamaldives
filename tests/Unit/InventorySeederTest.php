<?php

namespace Tests\Unit;

use App\Models\Accommodation;
use Database\Seeders\AccommodationInventorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_seeder_is_idempotent_for_key_properties(): void
    {
        $this->seed(AccommodationInventorySeeder::class);
        $this->seed(AccommodationInventorySeeder::class);

        $this->assertSame(1, Accommodation::query()->where('slug', 'soneva-fushi')->count());
        $this->assertSame(1, Accommodation::query()->where('slug', 'scubaspa-ying')->count());
        $this->assertSame(1, Accommodation::query()->where('slug', 'hulhule-island-hotel')->count());

        $this->assertTrue((bool) Accommodation::query()->where('slug', 'soneva-fushi')->value('published'));
        $this->assertTrue((bool) Accommodation::query()->where('slug', 'scubaspa-ying')->value('verified'));
        $this->assertSame('airport_hotel', Accommodation::query()->where('slug', 'hulhule-island-hotel')->value('property_subtype'));
    }
}
