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
            // Add new columns
            $table->timestamp('start_time')->after('parking_spot_id');
            $table->timestamp('end_time')->after('start_time');
            $table->decimal('total_cost', 8, 2)->after('total_price');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->after('is_paid');
            
            // Make existing columns nullable or update them
            $table->timestamp('reserved_at')->nullable()->change();
            $table->timestamp('parked_at')->nullable()->change();
            $table->timestamp('left_at')->nullable()->change();
            $table->decimal('total_price', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['start_time', 'end_time', 'total_cost', 'status']);
        });
    }
};