<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artifact_provenance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artifact_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Suggested values: purchase, donation, discovery, exhibition,
            // transfer, auction, publication, other — stored as a plain
            // string so owners can enter custom values without a migration.
            $table->string('type', 60)->default('other');

            // Short, scannable label for the record
            $table->string('title', 180);

            // Optional longer narrative
            $table->text('description')->nullable();

            // When the provenance event occurred
            $table->date('date')->nullable();

            // Where the event occurred
            $table->string('location', 180)->nullable();

            // External reference (auction catalogue, publication, etc.)
            $table->string('source_url')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['artifact_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifact_provenance');
    }
};
