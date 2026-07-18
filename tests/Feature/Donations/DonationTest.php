<?php

namespace Tests\Feature\Donations;

use App\Models\Artifact;
use App\Models\Donation;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_collector_can_initiate_a_donation_request(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id]);
        $museum = Museum::factory()->create(['verification_status' => Museum::VERIFICATION_VERIFIED]);

        $response = $this->actingAs($collector)->post(route('donations.store'), [
            'artifact_slug' => $artifact->slug,
            'museum_id' => $museum->id,
            'message' => 'I would like to donate this to your museum.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('donations', [
            'artifact_id' => $artifact->id,
            'donor_id' => $collector->id,
            'museum_id' => $museum->id,
            'status' => Donation::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function a_visitor_cannot_initiate_a_donation(): void
    {
        $visitor = User::factory()->create();
        $artifact = Artifact::factory()->create();
        $museum = Museum::factory()->create(['verification_status' => Museum::VERIFICATION_VERIFIED]);

        $response = $this->actingAs($visitor)->post(route('donations.store'), [
            'artifact_slug' => $artifact->slug,
            'museum_id' => $museum->id,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function a_donor_can_cancel_their_pending_donation(): void
    {
        $collector = User::factory()->collector()->create();
        $donation = Donation::factory()->create([
            'donor_id' => $collector->id,
            'status' => Donation::STATUS_PENDING,
        ]);

        $response = $this->actingAs($collector)->delete(route('donations.destroy', $donation));

        $response->assertRedirect();
        $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
    }

    /** @test */
    public function an_admin_can_approve_a_donation(): void
    {
        $admin = User::factory()->admin()->create();
        $donation = Donation::factory()->create(['status' => Donation::STATUS_PENDING]);

        $response = $this->actingAs($admin)->post(route('admin.donations.review', $donation), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Donation::STATUS_APPROVED, $donation->fresh()->status);
        $this->assertEquals($admin->id, $donation->fresh()->reviewed_by);
    }

    /** @test */
    public function an_admin_can_reject_a_donation(): void
    {
        $admin = User::factory()->admin()->create();
        $donation = Donation::factory()->create(['status' => Donation::STATUS_PENDING]);

        $response = $this->actingAs($admin)->post(route('admin.donations.review', $donation), [
            'action' => 'reject',
            'rejection_reason' => 'Not aligned with our current collection focus.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Donation::STATUS_REJECTED, $donation->fresh()->status);
    }

    /** @test */
    public function an_admin_can_transfer_an_approved_donation(): void
    {
        $admin = User::factory()->admin()->create();
        $museum = Museum::factory()->create();
        $donation = Donation::factory()->approved()->create(['museum_id' => $museum->id]);
        $artifact = $donation->artifact;

        $response = $this->actingAs($admin)->post(route('admin.donations.transfer', $donation), [
            'provenance_note' => 'Transferred from private collector.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Donation::STATUS_TRANSFERRED, $donation->fresh()->status);
        
        $artifact = $artifact->fresh();
        $this->assertEquals($museum->id, $artifact->museum_id);
        $this->assertNull($artifact->collector_id);
    }
}
