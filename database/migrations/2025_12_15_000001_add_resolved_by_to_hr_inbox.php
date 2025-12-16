<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hr_inbox', function (Blueprint $table) {
            $table->string('resolved_by', 32)->nullable()->after('resolved_at');
        });
    }

    public function down(): void
    {
        Schema::table('hr_inbox', function (Blueprint $table) {
            $table->dropColumn('resolved_by');
        });
    }
};
