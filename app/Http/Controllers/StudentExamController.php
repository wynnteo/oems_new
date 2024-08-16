<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;

class StudentExamController extends Controller
{
    public function show($examId)
    {
        // Fetch the exam details using the unique ID
        $exam = Exam::findOrFail($examId);

       // $student = Auth::user();
        $student = Student::find(1);
        // Initialize an empty error message
        $error = null;

        // Check if the student is enrolled in the course associated with the exam
        if (!$student->courses->contains($exam->course_id)) {
            $error = 'You are not enrolled in the course for this exam.';
        }

        // Check if the exam is active
        if ($exam->status !== 'active') {
            $error = 'This exam is not active.';
        }

        // Check if the current time is after the exam's start time
        if (now()->lt($exam->start_time)) {
            $error = 'This exam is not yet available.';
        }

        // Pass the exam data and the error message to the view
        return view('student.exam', compact('exam', 'error'));
    }

    public function start(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $student = Auth::user();

        // Re-check if the student is enrolled in the course
        if (!$student->courses->contains($exam->course_id)) {
            return redirect()->back()->withErrors('You are not enrolled in the course for this exam.');
        }

        // Re-check if the exam is active
        if ($exam->status !== 'active') {
            return redirect()->back()->withErrors('This exam is not active.');
        }

        // Re-check if the current time is after the exam's start time
        if (now()->lt($exam->start_time)) {
            return redirect()->back()->withErrors('This exam is not yet available.');
        }

        // Initialize the exam for the student
        // For example, you can create an exam session, log the start time, etc.

        return redirect()->route('exam.page', ['code' => $exam->code])
                     ->with('success', 'Exam started successfully!');
    }
}
