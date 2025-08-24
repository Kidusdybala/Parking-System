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
        Schema::table('parking_spots', function (Blueprint $table) {
            // Add missing columns for reservation tracking
            if (!Schema::hasColumn('parking_spots', 'reserved_by')) {
                $table->foreignId('reserved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('parking_spots', 'reserved_at')) {
                $table->timestamp('reserved_at')->nullable()->after('reserved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->dropForeign(['reserved_by']);
            $table->dropColumn(['reserved_by', 'reserved_at']);
        });
    }
};
