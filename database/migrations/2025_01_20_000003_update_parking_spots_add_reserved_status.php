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
        if (!Schema::hasTable('parking_spots')) {
            return;
        }

        Schema::table('parking_spots', function (Blueprint $table) {
            // Drop the old status enum and recreate with new values
            if (Schema::hasColumn('parking_spots', 'status')) {
                $table->dropColumn('status');
            }
        });
        
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->enum('status', ['available', 'reserved', 'occupied', 'maintenance'])
                  ->default('available')
                  ->after('hourly_rate');
                  
            // Add reserved_by field to track who reserved the spot
            $table->unsignedBigInteger('reserved_by')->nullable()->after('status');
            $table->foreign('reserved_by')->references('id')->on('users')->onDelete('set null');
            
            // Add reservation timestamp
            $table->timestamp('reserved_at')->nullable()->after('reserved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('parking_spots')) {
            return;
        }

        Schema::table('parking_spots', function (Blueprint $table) {
            $table->dropForeign(['reserved_by']);
            $table->dropColumn(['status', 'reserved_by', 'reserved_at']);
        });
        
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
        });
    }
};