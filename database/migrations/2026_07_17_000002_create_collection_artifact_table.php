<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_artifact', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('artifact_id')->constrained('artifacts')->cascadeOnDelete();

            // Curator/collector can drag-and-drop to reorder artifacts within a collection.
            $table->unsignedInteger('sort_order')->default(0);

            $table->primary(['collection_id', 'artifact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_artifact');
    }
};
