<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 15 — QR code registry for artifacts.
 *
 * Each artifact gets exactly one QR code record. The `token` (UUID) is the
 * public-facing identifier embedded in the QR URL:
 *   /qr/{token}  →  scan logged  →  redirect to /artifacts/{slug}
 *
 * Regenerating invalidates old QR prints (old token), so this is tracked
 * with `generation` so admins know if distributed materials are stale.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artifact_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->unsignedInteger('generation')->default(1); // increments on regenerate
            $table->unsignedBigInteger('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();

            $table->unique('artifact_id'); // one QR per artifact
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifact_qr_codes');
    }
};
