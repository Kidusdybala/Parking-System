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
        // For SQLite, we need to recreate the table to change enum to integer
        Schema::table('users', function (Blueprint $table) {
            // Add a temporary column
            $table->tinyInteger('role_temp')->default(1);
        });
        
        // Update the temporary column with integer values
        DB::table('users')->where('role', 'admin')->update(['role_temp' => 3]);
        DB::table('users')->where('role', 'client')->update(['role_temp' => 1]);
        
        // Drop the old role column and rename the temp column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_temp', 'role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add temporary enum column
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role_temp', ['admin', 'client'])->default('client');
        });
        
        // Convert integer values back to string values
        DB::table('users')->where('role', 3)->update(['role_temp' => 'admin']);
        DB::table('users')->where('role', 1)->update(['role_temp' => 'client']);
        
        // Drop the integer column and rename temp column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_temp', 'role');
        });
    }
};