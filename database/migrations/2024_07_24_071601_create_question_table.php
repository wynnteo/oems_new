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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id'); 
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->enum('question_type', ['true_false', 'single_choice', 'multiple_choice', 'fill_in_the_blank']);
            $table->text('question_text');
            $table->text('description')->nullable();
            $table->string('image_name')->nullable();
            $table->json('options')->nullable();
            $table->json('correct_answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
        });
        Schema::dropIfExists('questions');
    }
};
