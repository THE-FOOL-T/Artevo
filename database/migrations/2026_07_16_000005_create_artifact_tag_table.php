<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artifact_tag', function (Blueprint $table) {
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('artifact_tags')->cascadeOnDelete();
            $table->primary(['artifact_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifact_tag');
    }
};
