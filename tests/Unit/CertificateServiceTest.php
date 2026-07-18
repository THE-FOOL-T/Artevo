<?php

namespace Tests\Unit;

use App\Models\Artifact;
use App\Models\Certificate;
use App\Models\Donation;
use App\Models\Museum;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    private CertificateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CertificateService::class);
    }

    /** @test */
    public function it_can_issue_a_verification_certificate(): void
    {
        $admin = User::factory()->admin()->create();
        $artifact = Artifact::factory()->create();

        $certificate = $this->service->issueVerification($artifact, $admin);

        $this->assertInstanceOf(Certificate::class, $certificate);
        $this->assertEquals(Certificate::TYPE_VERIFICATION, $certificate->type);
        $this->assertEquals($artifact->id, $certificate->artifact_id);
        $this->assertEquals($admin->id, $certificate->issued_by);
        $this->assertNotNull($certificate->serial);
    }

    /** @test */
    public function issuing_verification_is_idempotent(): void
    {
        $admin = User::factory()->admin()->create();
        $artifact = Artifact::factory()->create();

        $cert1 = $this->service->issueVerification($artifact, $admin);
        $cert2 = $this->service->issueVerification($artifact, $admin);

        $this->assertEquals($cert1->id, $cert2->id);
        $this->assertEquals(1, Certificate::count());
    }

    /** @test */
    public function it_can_issue_a_donation_transfer_certificate(): void
    {
        $admin = User::factory()->admin()->create();
        $museum = Museum::factory()->create();
        $donor = User::factory()->create();
        $artifact = Artifact::factory()->create();
        
        $donation = Donation::factory()->create([
            'artifact_id' => $artifact->id,
            'museum_id' => $museum->id,
            'donor_id' => $donor->id,
        ]);

        $certificate = $this->service->issueDonationTransfer($donation, $admin);

        $this->assertInstanceOf(Certificate::class, $certificate);
        $this->assertEquals(Certificate::TYPE_DONATION_TRANSFER, $certificate->type);
        $this->assertEquals($artifact->id, $certificate->artifact_id);
        $this->assertEquals($admin->id, $certificate->issued_by);
        $this->assertEquals($museum->curator_id, $certificate->issued_to);
    }

    /** @test */
    public function it_can_revoke_a_certificate(): void
    {
        $certificate = Certificate::factory()->create();

        $revoked = $this->service->revoke($certificate, 'Artifact reported stolen.');

        $this->assertNotNull($revoked->revoked_at);
        $this->assertEquals('Artifact reported stolen.', $revoked->revocation_reason);
        $this->assertTrue($revoked->isRevoked());
        $this->assertFalse($revoked->isValid());
    }
}
