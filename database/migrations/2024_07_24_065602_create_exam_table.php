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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); 
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration')->default(0);
            $table->string('duration_unit')->nullable();
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('review_questions')->default(false);
            $table->integer('number_of_questions')->nullable();
            $table->boolean('show_answers')->default(false);
            $table->boolean('pagination')->default(false);
            $table->decimal('passing_grade', 5, 2)->nullable();
            $table->boolean('retake_allowed')->default(false);
            $table->integer('number_retake')->default(0);
            $table->boolean('allow_rating')->default(false);
            $table->timestamp('start_time');
            $table->enum('status', ['available', 'not_available'])->default('available');
            $table->string('access_code')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });
        Schema::dropIfExists('exams');
    }
};
