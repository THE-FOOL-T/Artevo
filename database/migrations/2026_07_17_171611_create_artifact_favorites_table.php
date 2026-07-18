<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artifact_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'artifact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifact_favorites');
    }
};
