<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogViewerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admins_can_view_the_activity_log(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index'));

        $response->assertOk();
    }

    /** @test */
    public function non_admins_cannot_view_the_activity_log(): void
    {
        $collector = User::factory()->collector()->create();

        $response = $this->actingAs($collector)->get(route('admin.activity-logs.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function the_log_can_be_filtered_by_action(): void
    {
        $admin = User::factory()->admin()->create();

        ActivityLog::create(['action' => 'user.login', 'description' => 'A login', 'ip_address' => '127.0.0.1']);
        ActivityLog::create(['action' => 'user.logout', 'description' => 'A logout', 'ip_address' => '127.0.0.1']);

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index', ['action' => 'user.login']));

        $response->assertOk();
        $response->assertSee('A login');
        $response->assertDontSee('A logout');
    }
}
