<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();

            // Owner — exactly one of museum_id or collector_id is set, never both.
            // Museum collections are curator-managed; collector collections are personal.
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('museum_id')->nullable()->constrained('museums')->cascadeOnDelete();
            $table->foreignId('collector_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->string('name', 180);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();

            // Visibility & merchandising
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);

            // Metrics — incremented without touching updated_at (same pattern as Museum)
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
