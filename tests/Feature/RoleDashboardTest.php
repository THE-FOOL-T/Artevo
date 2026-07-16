<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_sees_the_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboards.admin');
        $response->assertSee('Administrator Dashboard');
    }

    /** @test */
    public function a_curator_sees_the_curator_dashboard(): void
    {
        $curator = User::factory()->curator()->create();

        $response = $this->actingAs($curator)->get(route('dashboard'));

        $response->assertViewIs('dashboards.curator');
    }

    /** @test */
    public function a_collector_sees_the_collector_dashboard(): void
    {
        $collector = User::factory()->collector()->create();

        $response = $this->actingAs($collector)->get(route('dashboard'));

        $response->assertViewIs('dashboards.collector');
    }

    /** @test */
    public function a_visitor_sees_the_visitor_dashboard(): void
    {
        $visitor = User::factory()->visitor()->create();

        $response = $this->actingAs($visitor)->get(route('dashboard'));

        $response->assertViewIs('dashboards.visitor');
    }

    /** @test */
    public function non_admins_cannot_access_the_admin_users_page(): void
    {
        $collector = User::factory()->collector()->create();

        $response = $this->actingAs($collector)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function admins_can_access_the_admin_users_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
    }

    /** @test */
    public function guests_are_redirected_away_from_admin_routes(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }
}
