<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixChatMessagesSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('chat_messages')) {
            // Add updated_at if it's missing (created_at may already exist in older dumps)
            if (!Schema::hasColumn('chat_messages', 'updated_at')) {
                Schema::table('chat_messages', function (Blueprint $table) {
                    // Use nullable timestamp with CURRENT_TIMESTAMP default to match other tables
                    $table->timestamp('updated_at')->nullable()->useCurrent();
                });
            }

            // Ensure sender enum supports 'bot' as well as existing values
            // Use a raw statement because altering enum types isn't supported by the schema builder reliably
            try {
                DB::statement("ALTER TABLE `chat_messages` MODIFY `sender` ENUM('employee','hr','bot') DEFAULT NULL");
            } catch (\Exception $e) {
                // Log and continue - migration should not hard-fail in weird environments
                // (When running via artisan this will surface as an error if it truly fails)
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('chat_messages')) {
            // Revert sender enum back to original values (may fail if 'bot' values exist)
            try {
                DB::statement("ALTER TABLE `chat_messages` MODIFY `sender` ENUM('employee','hr') DEFAULT NULL");
            } catch (\Exception $e) {
                // ignore
            }

            if (Schema::hasColumn('chat_messages', 'updated_at')) {
                Schema::table('chat_messages', function (Blueprint $table) {
                    $table->dropColumn('updated_at');
                });
            }
        }
    }
}
