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
        // Drop the existing flaggedID column and recreate as auto-increment
        DB::statement('ALTER TABLE flaggedresponse DROP PRIMARY KEY');
        DB::statement('ALTER TABLE flaggedresponse DROP COLUMN flaggedID');
        DB::statement('ALTER TABLE flaggedresponse ADD flaggedID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to UUID
        DB::statement('ALTER TABLE flaggedresponse DROP PRIMARY KEY');
        DB::statement('ALTER TABLE flaggedresponse DROP COLUMN flaggedID');
        DB::statement('ALTER TABLE flaggedresponse ADD flaggedID VARCHAR(36) NOT NULL PRIMARY KEY FIRST');
    }
};
