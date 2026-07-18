<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logging_in_is_recorded(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Password123')]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'Password123']);

        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'user.login']);
    }

    /** @test */
    public function logging_out_is_recorded(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'));

        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'user.logout']);
    }

    /** @test */
    public function registering_is_recorded(): void
    {
        $this->post(route('register'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertDatabaseHas('activity_logs', ['action' => 'user.registered']);
    }

    /** @test */
    public function updating_the_profile_is_recorded(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'user.profile-updated']);
    }

    /** @test */
    public function changing_password_is_recorded(): void
    {
        $user = User::factory()->create(['password' => bcrypt('OldPassword123')]);

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'user.password-changed']);
    }

    /** @test */
    public function deleting_the_account_is_recorded_and_survives_the_deletion(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Password123')]);
        $userId = $user->id;

        $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'Password123']);

        $this->assertDatabaseHas('activity_logs', ['user_id' => null, 'action' => 'user.account-deleted']);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /** @test */
    public function a_role_change_is_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $collector = User::factory()->collector()->create();

        $this->actingAs($admin)->patch(route('admin.users.update-role', $collector), ['role' => 'curator']);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'role.changed',
            'user_id' => $admin->id,
            'subject_id' => $collector->id,
        ]);
    }

    /** @test */
    public function a_self_upgrade_is_recorded(): void
    {
        $visitor = User::factory()->visitor()->create();

        $this->actingAs($visitor)->post(route('role-upgrade.store'));

        $this->assertDatabaseHas('activity_logs', ['user_id' => $visitor->id, 'action' => 'role.self-upgraded']);
    }
}
