<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        return view('admin.exams.index', compact('exams'));
    }

    // Show the form for creating a new exam
    public function create()
    {
        $courses = Course::all();
        return view('admin.exams.create', compact('courses'));
    }

    // Store a newly created exam in storage
    public function store(Request $request)
    {
        $request->merge([
            'randomize_questions' => $request->has('randomize_questions'),
            'review_questions' => $request->has('review_questions'),
            'allow_rating' => $request->has('allow_rating'),
            'retake_allowed' => $request->has('retake_allowed'),
            'show_answers' => $request->has('show_answers'),
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'exam_code' => 'required|string',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'duration_unit' => 'required|string|in:minutes,hours',
            'start_time' => 'nullable|date',
            'number_of_questions' => 'nullable|integer|min:1',
            'randomize_questions' => 'nullable|boolean',
            'retake_allowed' => 'nullable|boolean',
            'number_retake' => 'nullable|integer|min:0',
            'passing_grade' => 'nullable|numeric|between:0,100',
            'review_questions' => 'nullable|boolean',
            'show_answers' => 'nullable|boolean',
            'status' => 'required|string|in:available,not_available',
            'access_code' => 'nullable|string|max:255',
            'allow_rating' => 'nullable|boolean',
            'course_id' => 'required|exists:courses,id',
        ]);

        Exam::create([
            'title' => $request->input('title'),
            'exam_code' => $request->input('exam_code'),
            'description' => $request->input('description'),
            'duration' => $request->input('duration'),
            'duration_unit' => $request->input('duration_unit'),
            'start_time' => $request->input('start_time'),
            'number_of_questions' => $request->input('number_of_questions'),
            'randomize_questions' => $request->input('randomize_questions'),
            'retake_allowed' => $request->input('retake_allowed'),
            'number_retake' => $request->input('number_retake'),
            'passing_grade' => $request->input('passing_grade'),
            'review_questions' => $request->input('review_questions'),
            'show_answers' => $request->input('show_answers'),
            'status' => $request->input('status'),
            'access_code' => $request->input('access_code'),
            'allow_rating' => $request->input('allow_rating'),
            'course_id' => $request->input('course_id'),
        ]);

        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    // Display the specified exam
    public function show(Exam $exam)
    {
        return view('admin.exams.show', compact('exam'));
    }

    // Show the form for editing the specified exam
    public function edit(Exam $exam)
    {
        $courses = Course::all();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    // Update the specified exam in storage
    public function update(Request $request, Exam $exam)
    {
        $request->merge([
            'randomize_questions' => $request->has('randomize_questions'),
            'review_questions' => $request->has('review_questions'),
            'allow_rating' => $request->has('allow_rating'),
            'retake_allowed' => $request->has('retake_allowed'),
            'show_answers' => $request->has('show_answers'),
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'exam_code' => 'required|string',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'duration_unit' => 'required|string|in:minutes,hours',
            'start_time' => 'nullable|date',
            'number_of_questions' => 'nullable|integer|min:1',
            'randomize_questions' => 'nullable|boolean',
            'retake_allowed' => 'nullable|boolean',
            'number_retake' => 'nullable|integer|min:0',
            'passing_grade' => 'nullable|numeric|between:0,100',
            'review_questions' => 'nullable|boolean',
            'show_answers' => 'nullable|boolean',
            'status' => 'required|string|in:available,not_available',
            'access_code' => 'nullable|string|max:255',
            'allow_rating' => 'nullable|boolean',
            'course_id' => 'required|exists:courses,id',
        ]);

        $exam->update($request->all());
        return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
    }

    // Remove the specified exam from storage
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }
}
