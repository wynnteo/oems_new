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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('set null');
            $table->string('certificate_number')->unique();
            $table->json('certificate_data'); // Student name, course, score, date etc
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->enum('status', ['generated', 'revoked'])->default('generated');
            $table->string('verification_code', 10)->unique();
            $table->string('file_path')->nullable();
            $table->index(['student_id', 'issued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
