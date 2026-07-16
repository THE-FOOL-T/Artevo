<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_change_another_users_role(): void
    {
        $admin = User::factory()->admin()->create();
        $collector = User::factory()->collector()->create();

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.update-role', $collector), ['role' => 'curator']);

        $response->assertSessionHasNoErrors();
        $this->assertSame('curator', $collector->fresh()->role);
    }

    /** @test */
    public function an_admin_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.update-role', $admin), ['role' => 'visitor']);

        $response->assertForbidden();
        $this->assertSame('admin', $admin->fresh()->role);
    }

    /** @test */
    public function a_non_admin_cannot_change_anyones_role(): void
    {
        $curator = User::factory()->curator()->create();
        $collector = User::factory()->collector()->create();

        $response = $this
            ->actingAs($curator)
            ->patch(route('admin.users.update-role', $collector), ['role' => 'admin']);

        $response->assertForbidden();
        $this->assertSame('collector', $collector->fresh()->role);
    }

    /** @test */
    public function the_role_must_be_a_valid_option(): void
    {
        $admin = User::factory()->admin()->create();
        $collector = User::factory()->collector()->create();

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.update-role', $collector), ['role' => 'superuser']);

        $response->assertSessionHasErrors('role');
    }
}
