<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('hr_inbox', function (Blueprint $table) {
        // Add a column to track if ticket is active for ongoing conversation
        $table->boolean('is_active')->default(true)->after('status');
    });
}

public function down()
{
    Schema::table('hr_inbox', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}

    /**
     * Reverse the migrations.
     */
 
};
