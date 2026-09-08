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
        Schema::create('monitor_states', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('UNKNOWN');
            $table->integer('consecutive_failures')->default(0);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_successful_check_at')->nullable();
            $table->timestamp('last_failed_check_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_states');
    }
};
