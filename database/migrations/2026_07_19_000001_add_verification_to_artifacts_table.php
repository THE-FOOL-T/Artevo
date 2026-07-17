<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 11 — adds the verification workflow columns to the artifacts table.
 *
 * As noted in the original artifacts migration, verification status was
 * deliberately left out of Phase 7 and reserved for this phase so that the
 * workflow and the column arrive together.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            // Lifecycle: unverified → pending (submitted) → verified or rejected
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])
                ->default('unverified')
                ->after('status');

            $table->foreignId('verified_by')
                ->nullable()
                ->after('verification_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable()
                ->after('verified_by');

            // Admin note attached to a verify or reject decision
            $table->text('verification_note')
                ->nullable()
                ->after('verified_at');

            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['verification_status', 'verified_at', 'verification_note']);
        });
    }
};
