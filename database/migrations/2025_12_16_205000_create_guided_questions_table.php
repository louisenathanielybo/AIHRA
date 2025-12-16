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
        Schema::create('guided_questions', function (Blueprint $table) {
            $table->id('gq_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('question_text');
            $table->text('answer_text')->nullable();
            $table->integer('LEVEL')->default(1);
            $table->integer('display_order')->default(0);
            $table->string('linked_intent')->nullable();
            $table->string('response_type')->default('text');
            $table->json('custom_response')->nullable();
            $table->string('category')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->foreign('parent_id')->references('gq_id')->on('guided_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guided_questions');
    }
};
