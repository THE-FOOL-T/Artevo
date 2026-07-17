<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 14 — donations table.
 *
 * Tracks artifact donation requests from collectors (or curators) to
 * a target museum. The workflow is:
 *   pending  →  approved  →  transferred  (ownership changes on artifact)
 *             ↘  rejected
 *
 * Ownership transfer is recorded as a provenance entry (type='donation').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // The artifact being donated
            $table->foreignId('artifact_id')
                ->constrained()
                ->cascadeOnDelete();

            // The user making the donation (collector or curator)
            $table->foreignId('donor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Target museum that will receive the artifact
            $table->foreignId('museum_id')
                ->constrained()
                ->cascadeOnDelete();

            // Admin or curator who reviewed the request
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // pending | approved | rejected | transferred
            $table->string('status', 30)->default('pending');

            // Optional message from donor to the receiving museum
            $table->text('message')->nullable();

            // Reason provided when rejecting the donation
            $table->text('rejection_reason')->nullable();

            // Auto-generated once transferred, e.g. "DON-000042"
            $table->string('certificate_number', 30)->nullable()->unique();

            // When the donor formally committed the piece
            $table->date('donated_at')->nullable();

            // When ownership was actually transferred in the system
            $table->timestamp('transferred_at')->nullable();

            // Notes that will appear in the auto-created provenance record
            $table->text('provenance_note')->nullable();

            $table->timestamps();

            $table->index(['artifact_id', 'status']);
            $table->index('museum_id');
            $table->index('donor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
