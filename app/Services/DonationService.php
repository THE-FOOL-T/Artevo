<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\ArtifactProvenance;
use App\Models\Donation;
use App\Models\Museum;
use App\Models\User;
use App\Notifications\DonationNotification;
use App\Services\CertificateService;
use Illuminate\Support\Str;


class DonationService
{

    public function __construct(
        private readonly CertificateService $certificateService,
    ) {}

    /**
     * Submit a new donation request.
     *
     * @param  array<string, mixed>  $data  Validated from StoreDonationRequest
     */
    public function requestDonation(Artifact $artifact, User $donor, Museum $museum, array $data): Donation
    {
        $donation = Donation::create([
            'artifact_id' => $artifact->id,
            'donor_id'    => $donor->id,
            'museum_id'   => $museum->id,
            'status'      => Donation::STATUS_PENDING,
            'message'     => $data['message'] ?? null,
            'donated_at'  => now()->toDateString(),
        ]);

        // Notify the target museum's curator
        if ($museum->curator) {
            $museum->curator->notify(new DonationNotification($donation, 'requested'));
        }

        return $donation;
    }

    /**
     * Admin approves the donation request.
     * Ownership has NOT changed yet — a separate transfer step does that.
     */
    public function approveDonation(Donation $donation, User $reviewer): Donation
    {
        $donation->update([
            'status'      => Donation::STATUS_APPROVED,
            'reviewed_by' => $reviewer->id,
        ]);

        // Notify the donor
        $donation->donor->notify(new DonationNotification($donation, 'approved'));

        return $donation->fresh(['artifact', 'museum', 'donor']);
    }

    /**
     * Admin rejects the donation request.
     */
    public function rejectDonation(Donation $donation, User $reviewer, string $reason): Donation
    {
        $donation->update([
            'status'           => Donation::STATUS_REJECTED,
            'reviewed_by'      => $reviewer->id,
            'rejection_reason' => $reason,
        ]);

        // Notify the donor with the reason
        $donation->donor->notify(new DonationNotification($donation, 'rejected'));

        return $donation->fresh(['artifact', 'museum', 'donor']);
    }

    /**
     * Admin triggers ownership transfer after approval.
     *
     * Actions performed:
     *  1. Artifact ownership re-assigned to the target museum.
     *  2. Provenance record created (type = 'donation').
     *  3. Donation status set to 'transferred' with a certificate number.
     *  4. Both parties notified.
     */
    public function transferOwnership(Donation $donation): Donation
    {
        $artifact = $donation->artifact;
        $museum   = $donation->museum;

        // 1. Re-assign artifact to the target museum
        $artifact->update([
            'museum_id'    => $museum->id,
            'collector_id' => null,
        ]);

        // 2. Create provenance record
        $maxOrder = $artifact->provenance()->max('sort_order') ?? -1;
        ArtifactProvenance::create([
            'artifact_id'  => $artifact->id,
            'recorded_by'  => $donation->reviewed_by,
            'type'         => 'donation',
            'title'        => "Donated to {$museum->name}",
            'description'  => $donation->provenance_note
                               ?? "Artifact donated by {$donation->donor->name} to {$museum->name}.",
            'date'         => now()->toDateString(),
            'sort_order'   => $maxOrder + 1,
        ]);

        // 3. Mark donation as transferred with certificate
        $certNumber = 'DON-' . str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT);
        $donation->update([
            'status'             => Donation::STATUS_TRANSFERRED,
            'certificate_number' => $certNumber,
            'transferred_at'     => now(),
        ]);

        // 4. Notify both parties
        $donation->donor->notify(new DonationNotification($donation->fresh(), 'transferred'));
        if ($museum->curator) {
            $museum->curator->notify(new DonationNotification($donation->fresh(), 'transferred'));
        }

        // Phase 16: Issue Certificate of Ownership Transfer
        // Use a system-level issuer (the admin who reviewed the donation).
        $reviewer = $donation->fresh()->reviewer;
        if ($reviewer) {
            $this->certificateService->issueDonationTransfer(
                $donation->fresh(['artifact', 'museum.curator', 'donor']),
                $reviewer
            );
        }

        return $donation->fresh(['artifact', 'museum', 'donor']);
    }

    /**
     * Donor cancels their own pending request.
     */
    public function cancelDonation(Donation $donation): void
    {
        $donation->delete();
    }
}
