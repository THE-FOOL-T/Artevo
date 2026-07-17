<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\Certificate;
use App\Models\Donation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Issue a Certificate of Authenticity after a successful verification.
     * Idempotent — if a valid certificate already exists for this artifact
     * type, it is returned rather than creating a duplicate.
     */
    public function issueVerification(Artifact $artifact, User $issuedBy): Certificate
    {
        // Check for an existing valid verification certificate
        $existing = Certificate::where('artifact_id', $artifact->id)
            ->where('type', Certificate::TYPE_VERIFICATION)
            ->whereNull('revoked_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        $certificate = Certificate::create([
            'artifact_id' => $artifact->id,
            'issued_to'   => $artifact->ownerId(),
            'issued_by'   => $issuedBy->id,
            'type'        => Certificate::TYPE_VERIFICATION,
            'serial'      => $this->generateSerial(),
        ]);

        \App\Jobs\GenerateCertificatePdf::dispatch($certificate);

        return $certificate;
    }

    /**
     * Issue a Certificate of Ownership Transfer after a donation is completed.
     */
    public function issueDonationTransfer(Donation $donation, User $issuedBy): Certificate
    {
        $artifact = $donation->artifact;

        $certificate = Certificate::create([
            'artifact_id'    => $artifact->id,
            'issued_to'      => $donation->museum->curator_id,
            'issued_by'      => $issuedBy->id,
            'type'           => Certificate::TYPE_DONATION_TRANSFER,
            'serial'         => $this->generateSerial(),
            'reference_type' => Donation::class,
            'reference_id'   => $donation->id,
            'notes'          => "Transferred from {$donation->donor->name} to {$donation->museum->name}.",
        ]);

        \App\Jobs\GenerateCertificatePdf::dispatch($certificate);

        return $certificate;
    }

    /**
     * Render a certificate as a PDF binary string ready for download.
     */
    public function renderPdf(Certificate $certificate): string
    {
        $certificate->load(['artifact.images', 'artifact.category', 'recipient', 'issuer']);

        $pdf = Pdf::loadView('certificates.pdf', ['certificate' => $certificate])
            ->setPaper('a4', 'landscape')
            ->setWarnings(false);

        return $pdf->output();
    }

    /**
     * Generate a unique human-readable serial number.
     * Format: ARTEVO-YYYY-XXXXXXXX (8 uppercase hex chars)
     */
    private function generateSerial(): string
    {
        do {
            $serial = 'ARTEVO-' . now()->year . '-' . strtoupper(Str::random(8));
        } while (Certificate::where('serial', $serial)->exists());

        return $serial;
    }

    /**
     * Revoke a certificate. Keeps the record for audit purposes.
     */
    public function revoke(Certificate $certificate, string $reason): Certificate
    {
        $certificate->update([
            'revoked_at'         => now(),
            'revocation_reason'  => $reason,
        ]);

        return $certificate->fresh();
    }
}
