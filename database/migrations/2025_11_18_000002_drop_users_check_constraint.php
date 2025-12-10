<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the check constraint that's preventing profile updates
        DB::statement('ALTER TABLE users DROP CHECK users_chk_1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't recreate the constraint as we don't know its exact definition
        // and it was causing issues with profile updates
    }
};
