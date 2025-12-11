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
        // Step 1: Create temporary columns for sequential IDs
        Schema::table('feedback', function (Blueprint $table) {
            $table->unsignedBigInteger('feedbackID_new')->nullable()->after('feedbackID');
        });
        
        Schema::table('flaggedresponse', function (Blueprint $table) {
            $table->unsignedBigInteger('flaggedID_new')->nullable()->after('flaggedID');
        });
        
        // Step 2: Assign sequential IDs based on creation date
        // For feedback table
        $feedbackEntries = DB::table('feedback')
            ->orderBy('timeStamp', 'asc')
            ->get();
        
        $counter = 1;
        foreach ($feedbackEntries as $entry) {
            DB::table('feedback')
                ->where('feedbackID', $entry->feedbackID)
                ->update(['feedbackID_new' => $counter]);
            $counter++;
        }
        
        // For flaggedresponse table
        $flaggedEntries = DB::table('flaggedresponse')
            ->orderBy('timeStamp', 'asc')
            ->get();
        
        $counter = 1;
        foreach ($flaggedEntries as $entry) {
            DB::table('flaggedresponse')
                ->where('flaggedID', $entry->flaggedID)
                ->update(['flaggedID_new' => $counter]);
            $counter++;
        }
        
        // Step 3: Use raw SQL to handle the conversion properly
        // For feedback table
        DB::statement('ALTER TABLE feedback DROP PRIMARY KEY, DROP COLUMN feedbackID');
        DB::statement('ALTER TABLE feedback CHANGE feedbackID_new feedbackID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        
        // For flaggedresponse table
        DB::statement('ALTER TABLE flaggedresponse DROP PRIMARY KEY, DROP COLUMN flaggedID');
        DB::statement('ALTER TABLE flaggedresponse CHANGE flaggedID_new flaggedID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal would be complex as we've lost the UUID values
        // This migration should be tested in development first
        throw new Exception('This migration cannot be reversed automatically');
    }
};
