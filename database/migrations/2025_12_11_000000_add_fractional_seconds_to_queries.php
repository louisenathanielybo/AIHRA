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
        // Alter queries table to support fractional seconds (microseconds)
        DB::statement('ALTER TABLE queries MODIFY questionTime DATETIME(6) NULL');
        DB::statement('ALTER TABLE queries MODIFY responseTime DATETIME(6) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to regular DATETIME
        DB::statement('ALTER TABLE queries MODIFY questionTime DATETIME NULL');
        DB::statement('ALTER TABLE queries MODIFY responseTime DATETIME NULL');
    }
};
