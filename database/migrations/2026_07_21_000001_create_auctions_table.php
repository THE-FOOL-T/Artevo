<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artifact_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('winner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['draft', 'active', 'closed', 'cancelled'])
                ->default('draft')
                ->index();

            $table->string('title', 220);
            $table->text('description')->nullable();
            $table->string('currency', 3)->default('USD');

            $table->decimal('reserve_price', 12, 2);
            $table->decimal('current_price', 12, 2);
            $table->decimal('bid_increment', 10, 2)->default(10.00);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable()->index();

            $table->unsignedInteger('bids_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();

            // Only one non-closed auction per artifact at a time
            $table->unique(['artifact_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
