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
        Schema::create('student_exam_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_exam_id'); 
            $table->foreign('student_exam_id')->references('id')->on('student_exams')->onDelete('cascade');
            $table->integer('attempt_number')->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_incorrect')->default(0);
            $table->float('score', 4, 2)->nullable();
            $table->json('review')->nullable(); // Detailed review info
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_exam_results', function (Blueprint $table) {
            $table->dropForeign(['student_exam_id']);
        });
        Schema::dropIfExists('student_exam_results');
    }
};
