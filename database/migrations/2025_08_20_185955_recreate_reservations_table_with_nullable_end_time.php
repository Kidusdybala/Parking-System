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
        // SQLite doesn't support changing column constraints properly
        // So we need to recreate the table
        
        // First, backup the data
        DB::statement('CREATE TABLE reservations_backup AS SELECT * FROM reservations');
        
        // Drop the original table
        Schema::dropIfExists('reservations');
        
        // Recreate the table with correct constraints
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parking_spot_id')->constrained()->onDelete('cascade');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('parked_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->decimal('total_price', 8, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            
            // New columns with proper nullable constraints
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable(); // This is the key fix
            $table->decimal('total_cost', 8, 2)->default(0);
            $table->enum('status', ['reserved', 'active', 'completed', 'cancelled', 'free'])->default('reserved');
            $table->timestamp('reservation_expires_at')->nullable();
            $table->timestamp('actual_start_time')->nullable();
            $table->timestamp('actual_end_time')->nullable();
        });
        
        // Restore the data
        DB::statement('INSERT INTO reservations SELECT * FROM reservations_backup');
        
        // Drop the backup table
        DB::statement('DROP TABLE reservations_backup');
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