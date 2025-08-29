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
            // Add missing columns that the code expects
            if (!Schema::hasColumn('reservations', 'start_time')) {
                $table->timestamp('start_time')->nullable()->after('reservation_expires_at');
            }
            
            if (!Schema::hasColumn('reservations', 'end_time')) {
                $table->timestamp('end_time')->nullable()->after('start_time');
            }
            
            if (!Schema::hasColumn('reservations', 'total_cost')) {
                $table->decimal('total_cost', 8, 2)->default(0.00)->after('end_time');
            }
            
            if (!Schema::hasColumn('reservations', 'actual_start_time')) {
                $table->timestamp('actual_start_time')->nullable()->after('total_cost');
            }
            
            if (!Schema::hasColumn('reservations', 'actual_end_time')) {
                $table->timestamp('actual_end_time')->nullable()->after('actual_start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'total_cost', 'actual_start_time', 'actual_end_time']);
        });
    }
};
