<?php

namespace Tests\Feature\Admin;

use App\Models\Museum;
use App\Models\User;
use App\Notifications\MuseumVerificationUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MuseumVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_verify_a_museum(): void
    {
        $admin = User::factory()->admin()->create();
        $museum = Museum::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.museums.verification.update', $museum), [
            'verification_status' => 'verified',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue($museum->fresh()->isVerified());
    }

    /** @test */
    public function verifying_a_museum_notifies_its_curator(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $this->actingAs($admin)->patch(route('admin.museums.verification.update', $museum), [
            'verification_status' => 'verified',
        ]);

        Notification::assertSentTo($curator, MuseumVerificationUpdated::class);
    }

    /** @test */
    public function the_verification_change_is_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $museum = Museum::factory()->create();

        $this->actingAs($admin)->patch(route('admin.museums.verification.update', $museum), [
            'verification_status' => 'verified',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'museum.verification-changed',
            'user_id' => $admin->id,
            'subject_id' => $museum->id,
        ]);
    }

    /** @test */
    public function a_curator_cannot_verify_their_own_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $response = $this->actingAs($curator)->patch(route('admin.museums.verification.update', $museum), [
            'verification_status' => 'verified',
        ]);

        $response->assertForbidden();
        $this->assertFalse($museum->fresh()->isVerified());
    }

    /** @test */
    public function an_invalid_status_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $museum = Museum::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.museums.verification.update', $museum), [
            'verification_status' => 'super-verified',
        ]);

        $response->assertSessionHasErrors('verification_status');
    }

    /** @test */
    public function no_notification_is_sent_when_the_status_does_not_change(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $museum = Museum::factory()->create(); // defaults to pending

        $this->actingAs($admin)->patch(route('admin.museums.verification.update', $museum), [
            'verification_status' => 'pending',
        ]);

        Notification::assertNothingSent();
    }
}
