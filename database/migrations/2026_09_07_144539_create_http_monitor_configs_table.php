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
        Schema::create('http_monitor_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('method')->default('GET');
            $table->boolean('follow_redirects')->default(false);
            $table->json('expected_status_codes')->default(json_encode([200]));
            $table->json('expected_response_body')->nullable();
            $table->integer('max_response_time_ms')->nullable();
            // keydown containers 
            $table->string('keyword_contains')->nullable();
            $table->string('keyword_not_contains')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('http_monitor_configs');
    }
};
