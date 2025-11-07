<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('chat_messages', 'updated_at')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                // make it nullable to avoid issues with existing rows
                $table->timestamp('updated_at')->nullable()->after('created_at');
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
        if (Schema::hasColumn('chat_messages', 'updated_at')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
};
