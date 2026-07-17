<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhibition_section_artifact', function (Blueprint $table) {
            $table->foreignId('exhibition_section_id')
                ->constrained('exhibition_sections')
                ->cascadeOnDelete();

            $table->foreignId('artifact_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')->default(0);

            // An artifact can appear only once per section
            $table->primary(['exhibition_section_id', 'artifact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhibition_section_artifact');
    }
};
