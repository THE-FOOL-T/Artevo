<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 16 — Certificates of Authenticity.
 *
 * A certificate is issued in two situations:
 *  1. An artifact passes admin verification (type = 'verification')
 *  2. An artifact is transferred via a donation (type = 'donation_transfer')
 *
 * The `serial` is a publicly-shareable identifier. Anyone with the serial
 * can visit /certificates/{serial} to verify the certificate is genuine.
 *
 * `revoked_at` allows an admin to invalidate a certificate without
 * deleting the audit trail.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();

            // Type distinguishes the reason for issuance
            $table->string('type', 40)->default('verification');
            // verification | donation_transfer

            // Human-readable unique serial — e.g. ARTEVO-2026-AB12CD34
            $table->string('serial', 30)->unique();

            // Optional reference (donation ID, provenance record ID, etc.)
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->index(['reference_type', 'reference_id']);

            // Free-text notes added by the issuer
            $table->text('notes')->nullable();

            // Soft revocation — doesn't delete history
            $table->timestamp('revoked_at')->nullable();
            $table->string('revocation_reason')->nullable();

            $table->timestamps();

            $table->index(['artifact_id', 'type']);
            $table->index('serial');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
