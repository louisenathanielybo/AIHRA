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
            // Add response and resolution deadline fields
            $table->timestamp('response_deadline')->nullable()->after('created_at');
            $table->timestamp('resolution_deadline')->nullable()->after('response_deadline');
            $table->timestamp('responded_at')->nullable()->after('resolution_deadline');
            $table->timestamp('resolved_at')->nullable()->after('responded_at');
            $table->boolean('is_expired')->default(false)->after('resolved_at');
            
            // Add index for efficient expiration checks
            $table->index(['is_expired', 'response_deadline']);
            $table->index(['status', 'response_deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_inbox', function (Blueprint $table) {
            $table->dropIndex(['is_expired', 'response_deadline']);
            $table->dropIndex(['status', 'response_deadline']);
            $table->dropColumn([
                'response_deadline',
                'resolution_deadline',
                'responded_at',
                'resolved_at',
                'is_expired'
            ]);
        });
    }
};
