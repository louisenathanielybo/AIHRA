<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Expand priority to include 'urgent'
        DB::statement("ALTER TABLE hr_inbox MODIFY COLUMN priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'low'");
    }

    public function down(): void
    {
        // Revert to previous enum without 'urgent' (best effort)
        DB::statement("ALTER TABLE hr_inbox MODIFY COLUMN priority ENUM('low','medium','high') NOT NULL DEFAULT 'low'");
    }
};
