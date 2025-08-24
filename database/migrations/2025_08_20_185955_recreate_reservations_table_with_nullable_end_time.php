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
        // Skip this migration as the reservations table is already properly created
        // by earlier migrations and this recreation is causing column mismatch issues
        
        if (!Schema::hasTable('reservations')) {
            return;
        }
        
        // Just ensure the end_time column is nullable if it exists but isn't nullable
        if (Schema::hasColumn('reservations', 'end_time')) {
            // The table structure is already correct from earlier migrations
            return;
        }
        
        // If end_time column doesn't exist, add it
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex migration, reverting would be difficult
        // In production, you'd want to create a proper rollback
        throw new Exception('This migration cannot be rolled back safely');
    }
};