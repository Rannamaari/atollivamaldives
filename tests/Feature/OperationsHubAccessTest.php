<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsHubAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_operations_hub_is_inaccessible_to_unauthenticated_users(): void
    {
        $this->get(route('filament.admin.pages.dashboard'))
            ->assertRedirect('/admin/login');
    }

    public function test_existing_public_routes_still_load(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('faq'))->assertOk();
        $this->get(route('request-quote'))->assertOk();
    }

    public function test_existing_filament_login_still_works(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_authenticated_user_can_access_operations_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('filament.admin.pages.dashboard'))
            ->assertOk();
    }
}
