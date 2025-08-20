<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the reservations table to support new statuses
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the old status enum and recreate with new values
            if (Schema::hasColumn('reservations', 'status')) {
                $table->dropColumn('status');
            }
        });
        
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['reserved', 'active', 'completed', 'cancelled', 'free'])
                  ->default('reserved')
                  ->after('is_paid');
            
            // Add reservation expiry time if it doesn't exist
            if (!Schema::hasColumn('reservations', 'reservation_expires_at')) {
                $table->timestamp('reservation_expires_at')->nullable()->after('reserved_at');
            }
            
            // Add actual start and end times for better tracking if they don't exist
            if (!Schema::hasColumn('reservations', 'actual_start_time')) {
                $table->timestamp('actual_start_time')->nullable()->after('parked_at');
            }
            if (!Schema::hasColumn('reservations', 'actual_end_time')) {
                $table->timestamp('actual_end_time')->nullable()->after('left_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['status', 'reservation_expires_at', 'actual_start_time', 'actual_end_time']);
        });
        
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
        });
    }
};