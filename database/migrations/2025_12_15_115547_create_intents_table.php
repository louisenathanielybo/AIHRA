<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/[timestamp]_create_intents_table.php
public function up()
{
    Schema::create('intents', function (Blueprint $table) {
        $table->id();
        $table->uuid('intent_id')->unique();
        $table->string('intent_name');
        $table->string('display_name');
        $table->text('description')->nullable();
        $table->json('training_phrases')->nullable();
        $table->json('responses')->nullable();
        $table->json('parameters')->nullable();
        $table->integer('training_phrases_count')->default(0);
        $table->integer('responses_count')->default(0);
        $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intents');
    }
};
