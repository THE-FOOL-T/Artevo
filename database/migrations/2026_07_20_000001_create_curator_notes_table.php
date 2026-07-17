<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 12 — curator_notes table.
 *
 * Notes are strictly private: written by the artifact owner or an admin,
 * never surfaced on the public artifact page. They record internal
 * observations, condition checks, recommendations, and admin comments.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curator_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artifact_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // observation | condition-check | recommendation | admin-note
            $table->string('note_type', 40)->default('observation');

            // Supports simple HTML from a textarea
            $table->text('body');

            // Pinned notes sort to the top of the list
            $table->boolean('is_pinned')->default(false);

            $table->timestamps();

            $table->index(['artifact_id', 'is_pinned', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curator_notes');
    }
};
