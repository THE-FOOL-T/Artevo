<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An artifact belongs to exactly one of `museum_id` (added by a
     * curator, part of an institutional collection) or `collector_id`
     * (added by a collector, part of a private collection) — enforced in
     * ArtifactService rather than a DB constraint, since that XOR rule
     * isn't portable across database engines. `created_by` always
     * records who actually submitted it, regardless of which it is.
     *
     * Verification status is deliberately NOT a column here — it's added
     * in Phase 11 alongside the verification workflow itself, the same
     * way Phase 6 added museum verification only once that workflow
     * existed.
     */
    public function up(): void
    {
        Schema::create('artifacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('museum_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('collector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained('artifact_categories');
            $table->foreignId('material_id')->nullable()->constrained('artifact_materials')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('artifact_code', 20)->unique()->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();

            $table->string('civilization')->nullable();
            $table->string('era')->nullable();
            $table->string('century')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('region')->nullable();
            $table->string('discovery_location')->nullable();
            $table->string('language')->nullable();

            $table->string('dimensions')->nullable();
            $table->string('weight')->nullable();
            $table->string('condition', 40)->nullable();
            $table->decimal('estimated_value', 12, 2)->nullable();

            $table->string('status', 20)->default('private')->index(); // public, private, archived

            $table->softDeletes();
            $table->timestamps();

            $table->index(['museum_id', 'status']);
            $table->index(['collector_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifacts');
    }
};
