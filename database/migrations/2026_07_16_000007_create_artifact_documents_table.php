<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artifact_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artifact_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('document_path');
            $table->string('document_type', 60)->nullable(); // e.g. Certificate, Manuscript, Research Paper
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artifact_documents');
    }
};
