<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the two things Phase 5 deliberately left out: a museum-level
     * verification status (distinct from artifact verification, which
     * is Phase 11) and a simple page-view counter that feeds the new
     * per-museum dashboard's "Visitors" metric honestly.
     */
    public function up(): void
    {
        Schema::table('museums', function (Blueprint $table) {
            $table->string('verification_status', 20)->default('pending')->after('featured')->index();
            $table->unsignedInteger('views_count')->default(0)->after('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('museums', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'views_count']);
        });
    }
};
