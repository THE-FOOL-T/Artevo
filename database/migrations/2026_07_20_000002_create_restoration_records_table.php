<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 12 — restoration_records table.
 *
 * Tracks every physical or analytical intervention on an artifact.
 * Unlike curator notes, restoration records are public — conservation
 * history adds scholarly value and builds visitor trust.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restoration_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artifact_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // cleaning | conservation | repair | analysis |
            // documentation | examination | other
            $table->string('category', 60)->default('other');

            $table->string('title', 180);

            $table->text('description')->nullable();

            $table->string('conservator_name', 120)->nullable();

            $table->string('institution', 180)->nullable();

            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();

            // Optional reference to an existing artifact document
            $table->foreignId('artifact_document_id')
                ->nullable()
                ->constrained('artifact_documents')
                ->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['artifact_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restoration_records');
    }
};
