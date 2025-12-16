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
        Schema::table('hr_inbox', function (Blueprint $table) {
            // Add the missing 'assigned_to' column
            $table->string('assigned_to', 50)->nullable()->after('id');
            // Adjust the position (after('id')) based on your table structure
            // You can use 'nullable()' if existing records don't have this value
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_inbox', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }
};