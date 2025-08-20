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
            // Drop columns that don't exist in rms.sql
            $table->dropColumn(['start_time', 'end_time', 'total_cost']);
            
            // Update status enum to match rms.sql structure
            $table->enum('status', ['free', 'active'])->default('free')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Add back the dropped columns
            $table->timestamp('start_time')->after('parking_spot_id');
            $table->timestamp('end_time')->after('start_time');
            $table->decimal('total_cost', 8, 2)->after('total_price');
            
            // Revert status enum
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->change();
        });
    }
};