<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add an external photo URL field to museums.
     * Used as a display fallback when no cover image has been uploaded
     * via MuseumMediaService — for seeded institutions that have a known
     * public-domain photo (e.g. from Wikimedia Commons).
     */
    public function up(): void
    {
        Schema::table('museums', function (Blueprint $table) {
            $table->string('cover_photo_url', 1024)->nullable()->after('cover_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('museums', function (Blueprint $table) {
            $table->dropColumn('cover_photo_url');
        });
    }
};
