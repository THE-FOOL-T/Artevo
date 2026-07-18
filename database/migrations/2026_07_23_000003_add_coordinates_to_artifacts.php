<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 15 — Geographic coordinates for artifacts.
 *
 * Two independent coordinate pairs:
 *  - origin_*    = where the piece was created / its cultural homeland
 *  - discovery_* = where it was physically found / excavated
 *
 * Both are optional; either or both may be displayed on the artifact map.
 * Precision of 7 decimal places gives ~1 cm accuracy, matching museums.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            // Country / civilization of origin pin (gold marker)
            $table->decimal('origin_latitude', 10, 7)->nullable()->after('country_of_origin');
            $table->decimal('origin_longitude', 10, 7)->nullable()->after('origin_latitude');

            // Physical discovery / excavation site pin (teal marker)
            $table->decimal('discovery_latitude', 10, 7)->nullable()->after('discovery_location');
            $table->decimal('discovery_longitude', 10, 7)->nullable()->after('discovery_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropColumn([
                'origin_latitude',
                'origin_longitude',
                'discovery_latitude',
                'discovery_longitude',
            ]);
        });
    }
};
