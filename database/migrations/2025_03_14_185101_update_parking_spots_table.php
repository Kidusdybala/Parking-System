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
            // Add new columns
            $table->string('spot_number')->unique()->after('id');
            $table->string('location')->after('name');
            $table->decimal('hourly_rate', 8, 2)->after('price_per_hour');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available')->after('is_reserved');
            
            // Drop old columns
            $table->dropColumn(['price_per_hour', 'is_reserved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            // Add back old columns
            $table->decimal('price_per_hour', 8, 2)->after('name');
            $table->boolean('is_reserved')->default(false)->after('price_per_hour');
            
            // Drop new columns
            $table->dropColumn(['spot_number', 'location', 'hourly_rate', 'status']);
        });
    }
};