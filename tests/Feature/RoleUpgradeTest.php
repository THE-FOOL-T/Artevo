<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleUpgradeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_visitor_can_upgrade_to_collector(): void
    {
        $visitor = User::factory()->visitor()->create();

        $response = $this->actingAs($visitor)->post(route('role-upgrade.store'));

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('collector', $visitor->fresh()->role);
    }

    /** @test */
    public function a_collector_cannot_upgrade_again(): void
    {
        $collector = User::factory()->collector()->create();

        $response = $this->actingAs($collector)->post(route('role-upgrade.store'));

        $response->assertForbidden();
    }

    /** @test */
    public function a_curator_cannot_use_the_visitor_upgrade_route(): void
    {
        $curator = User::factory()->curator()->create();

        $response = $this->actingAs($curator)->post(route('role-upgrade.store'));

        $response->assertForbidden();
    }
}
