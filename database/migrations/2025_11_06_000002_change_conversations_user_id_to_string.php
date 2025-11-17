<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConversationsUserIdToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // If the column is currently an integer (from the previous migration) we need
        // to replace it with a string so it can store employee numbers like "EMP001".
        if (Schema::hasTable('conversations')) {
            // Drop the old integer column if it exists
            if (Schema::hasColumn('conversations', 'user_id')) {
                Schema::table('conversations', function (Blueprint $table) {
                    // Drop index first if present
                    try {
                        $table->dropIndex(['user_id']);
                    } catch (\Exception $e) {
                        // ignore if index doesn't exist or drop fails
                    }

                    // Drop the column (safe even if it was integer)
                    $table->dropColumn('user_id');
                });
            }

            // Add string user_id column
            Schema::table('conversations', function (Blueprint $table) {
                $table->string('user_id')->nullable()->after('id');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('conversations')) {
            // Remove the string user_id and restore as unsignedBigInteger
            if (Schema::hasColumn('conversations', 'user_id')) {
                Schema::table('conversations', function (Blueprint $table) {
                    try {
                        $table->dropIndex(['user_id']);
                    } catch (\Exception $e) {
                        // ignore
                    }
                    $table->dropColumn('user_id');
                });
            }

            Schema::table('conversations', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index('user_id');
            });
        }
    }
}
