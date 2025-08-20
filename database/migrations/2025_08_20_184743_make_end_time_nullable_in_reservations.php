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
        Schema::table('reservations', function (Blueprint $table) {
            // Make end_time nullable
            $table->timestamp('end_time')->nullable()->change();
            $table->timestamp('start_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Revert back to NOT NULL (if needed)
            $table->timestamp('end_time')->nullable(false)->change();
            $table->timestamp('start_time')->nullable(false)->change();
        });
    }
};