<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use App\Notifications\RoleChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_role_change_notifies_the_affected_user(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $collector = User::factory()->collector()->create();

        $this->actingAs($admin)->patch(route('admin.users.update-role', $collector), ['role' => 'curator']);

        Notification::assertSentTo($collector, RoleChanged::class);
    }

    /** @test */
    public function changing_password_notifies_the_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['password' => bcrypt('OldPassword123')]);

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        Notification::assertSentTo($user, PasswordChangedNotification::class);
    }

    /** @test */
    public function no_notification_is_sent_when_the_role_does_not_actually_change(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $collector = User::factory()->collector()->create();

        $this->actingAs($admin)->patch(route('admin.users.update-role', $collector), ['role' => 'collector']);

        Notification::assertNothingSent();
    }

    /** @test */
    public function a_user_can_view_their_notifications_page(): void
    {
        $user = User::factory()->create();
        $user->notify(new PasswordChangedNotification());

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee('Password changed');
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new PasswordChangedNotification());
        $notification = $user->notifications()->first();

        $this->actingAs($user)->post(route('notifications.read', $notification->id));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function a_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new PasswordChangedNotification());
        $user->notify(new PasswordChangedNotification());

        $this->actingAs($user)->post(route('notifications.read-all'));

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    /** @test */
    public function a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $owner->notify(new PasswordChangedNotification());
        $notification = $owner->notifications()->first();

        $this->actingAs($intruder)->post(route('notifications.read', $notification->id))->assertNotFound();
    }
}
