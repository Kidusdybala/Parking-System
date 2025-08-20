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
        // First, update existing role values to string equivalents
        DB::table('users')->where('role', 1)->update(['role' => 'admin']);
        DB::table('users')->where('role', 2)->update(['role' => 'client']);
        DB::table('users')->whereNotIn('role', ['admin', 'client'])->update(['role' => 'client']);

        // Now change the column type to enum with only admin and client
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'client'])->default('client')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to integer values
        DB::table('users')->where('role', 'admin')->update(['role' => 1]);
        DB::table('users')->where('role', 'client')->update(['role' => 2]);

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('role')->default(2)->change();
        });
    }
};