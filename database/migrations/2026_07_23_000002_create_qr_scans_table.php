<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 15 — Per-scan analytics log.
 *
 * Lightweight audit trail for every time a QR code is scanned.
 * We don't store personal data — only anonymous signals (IP, UA, referrer).
 * GDPR note: IP is stored for rate-limiting and abuse detection only;
 * if this becomes a compliance concern, hash it server-side.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_qr_code_id')
                ->constrained('artifact_qr_codes')
                ->cascadeOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();

            // Which QR generation was scanned (helps detect stale prints)
            $table->unsignedInteger('generation')->default(1);

            $table->timestamp('scanned_at')->useCurrent();

            $table->index('artifact_qr_code_id');
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
    }
};
