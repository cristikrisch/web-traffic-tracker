<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            // Truncated IP for coarse-grained geo or dedupe
            $table->string('ip_trunc', 45)->nullable()->after('ip')->index();

            // Hashed IP for uniqueness/returning visitor detection
            $table->char('ip_hash', 64)->nullable()->after('ip_trunc')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropColumn(['ip_trunc', 'ip_hash']);
        });
    }
};
