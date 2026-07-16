<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RoleGatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function every_role_can_view_a_dashboard(): void
    {
        foreach (['admin', 'curator', 'collector', 'visitor'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->assertTrue(Gate::forUser($user)->allows('view-dashboard'));
        }
    }

    /** @test */
    public function only_admins_can_access_analytics_export_reports_and_activity_logs(): void
    {
        $admin = User::factory()->admin()->create();
        $curator = User::factory()->curator()->create();

        foreach (['access-analytics', 'export-reports', 'view-activity-logs'] as $gate) {
            $this->assertTrue(Gate::forUser($admin)->allows($gate));
            $this->assertFalse(Gate::forUser($curator)->allows($gate));
        }
    }
}
