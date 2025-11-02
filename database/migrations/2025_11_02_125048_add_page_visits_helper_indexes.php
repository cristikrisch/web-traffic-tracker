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
        Schema::table('page_visits', function (Blueprint $t) {
            $t->index(['page_id', 'visit_date'], 'idx_page_date'); //Uniques per day across all pages
            $t->index(['visit_date', 'page_id'], 'idx_date_page'); //Top pages in range
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex(['idx_page_date', 'idx_date_page']);
        });
    }
};
