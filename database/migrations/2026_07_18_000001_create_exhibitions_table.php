<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhibitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('museum_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name', 180);
            $table->string('slug')->unique();
            $table->string('tagline', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();

            // Lifecycle: draft → published → archived
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            $table->boolean('is_featured')->default(false);

            // Optional physical or virtual event dates
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // null = free admission; positive value = paid
            $table->decimal('admission_fee', 10, 2)->nullable();

            // Physical venue or virtual URL
            $table->string('location', 255)->nullable();

            $table->unsignedBigInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhibitions');
    }
};
