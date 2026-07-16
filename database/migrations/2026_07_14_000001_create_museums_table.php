<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Core museum profile data. Verification status and the map-specific
     * UI are Phase 6's job — this phase covers the profile itself: who
     * manages it, what it says, and where it is.
     */
    public function up(): void
    {
        Schema::create('museums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curator_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->unsignedSmallInteger('foundation_year')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->json('opening_hours')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('featured')->default(false)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('museums');
    }
};
