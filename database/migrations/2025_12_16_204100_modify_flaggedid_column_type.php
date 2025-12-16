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
        // Modify flaggedID to be varchar(36) to accommodate UUIDs
        DB::statement('ALTER TABLE flaggedresponse MODIFY flaggedID VARCHAR(36) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original type if needed
        DB::statement('ALTER TABLE flaggedresponse MODIFY flaggedID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }
};
