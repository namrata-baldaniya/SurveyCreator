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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

        $table->unsignedBigInteger('survey_id')->default(1);
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('question_option_id')->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->text('answer_text')->nullable(); 
            $table->timestamps();
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('question_option_id')->references('id')->on('question_options')->onDelete('cascade'); 
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
