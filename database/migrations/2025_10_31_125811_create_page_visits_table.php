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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->ulid('visitor_id');
            $table->foreign('visitor_id')->references('id')->on('visitors')->cascadeOnDelete();

            $table->unsignedBigInteger('page_id');
            $table->foreign('page_id')->references('id')->on('pages')->cascadeOnDelete();

            $table->string('full_url', 2048);
            $table->string('referrer', 2048)->nullable();

            $table->string('utm_source', 64)->nullable()->index();
            $table->string('utm_medium', 64)->nullable()->index();
            $table->string('utm_campaign', 128)->nullable()->index();
            $table->string('utm_term', 128)->nullable()->index();
            $table->string('utm_content', 128)->nullable()->index();

            $table->string('ip', 45)->nullable()->index();
            $table->text('user_agent')->nullable();

            $table->timestamp('visited_at')->useCurrent()->index();

            $table->date('visit_date')->storedAs('DATE(`visited_at`)')->index();

            $table->timestamps();

            $table->unique(['page_id', 'visitor_id', 'visit_date'], 'uniq_page_visitor_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
