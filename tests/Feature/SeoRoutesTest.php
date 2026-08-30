<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Atoll;
use App\Models\Island;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_resort_canonical_route_loads(): void
    {
        $resort = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Baros Maldives',
            'slug' => 'baros-maldives',
            'published' => true,
        ]);

        $this->get(route('resorts.show', $resort))
            ->assertOk()
            ->assertSee('Baros Maldives');
    }

    public function test_liveaboard_canonical_route_loads(): void
    {
        $liveaboard = Accommodation::create([
            'type' => 'liveaboard',
            'status' => 'published',
            'name' => 'Blue Horizon Maldives',
            'slug' => 'blue-horizon-maldives',
            'published' => true,
        ]);

        $this->get(route('liveaboards.show', $liveaboard))
            ->assertOk()
            ->assertSee('Blue Horizon Maldives');
    }

    public function test_package_canonical_route_loads(): void
    {
        $package = Accommodation::create([
            'type' => 'package',
            'status' => 'published',
            'name' => 'Maldives Romantic Escape',
            'slug' => 'maldives-romantic-escape',
            'property_subtype' => 'honeymoon',
            'published' => true,
        ]);

        $this->get(route('packages.show', ['category' => 'honeymoon', 'accommodation' => $package]))
            ->assertOk()
            ->assertSee('Maldives Romantic Escape');
    }

    public function test_guesthouse_hierarchical_route_loads(): void
    {
        $atoll = Atoll::create([
            'name' => 'Kaafu Atoll',
            'slug' => 'kaafu-atoll',
            'status' => 'published',
        ]);

        $island = Island::create([
            'atoll_id' => $atoll->id,
            'name' => 'Maafushi',
            'slug' => 'maafushi',
            'status' => 'published',
        ]);

        $guesthouse = Accommodation::create([
            'type' => 'guesthouse',
            'status' => 'published',
            'name' => 'Kuredhi Beach Inn',
            'slug' => 'kuredhi-beach-inn',
            'atoll_id' => $atoll->id,
            'island_id' => $island->id,
            'atoll' => 'Kaafu Atoll',
            'island' => 'Maafushi',
            'published' => true,
        ]);

        $this->get(route('guesthouses.show', ['atoll' => $atoll, 'island' => $island, 'accommodation' => $guesthouse]))
            ->assertOk()
            ->assertSee('Kuredhi Beach Inn');
    }

    public function test_legacy_travel_product_url_redirects_to_canonical_route_with_query_string(): void
    {
        $resort = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Baros Maldives',
            'slug' => 'baros-maldives',
            'published' => true,
        ]);

        $this->get('/travel-products/baros-maldives?check_in=2026-09-05&adults=2')
            ->assertRedirect('/resorts/baros-maldives?adults=2&check_in=2026-09-05');
    }

    public function test_old_guest_houses_listing_redirects_to_new_guesthouses_listing(): void
    {
        $this->get('/guest-houses')
            ->assertRedirect('/guesthouses');
    }
}
