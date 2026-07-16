<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A generic, append-only audit trail. `subject` is a polymorphic
     * relation left nullable/unused by this phase (no Artifact/Auction/
     * Museum models exist yet) but ready for Phase 5+ to attach log
     * entries directly to the record an action was performed on.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60)->index(); // e.g. "user.login", "role.changed"
            $table->string('description');
            $table->nullableMorphs('subject'); // ready for Phase 5+ (Artifact, Auction, Museum...)
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
