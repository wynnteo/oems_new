<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;

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
        $request->validate([
            'title' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ]);

        Exam::create($request->all());

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
        return view('admin.exams.edit', compact('exam'));
    }

    // Update the specified exam in storage
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'exam_date' => 'required|date',
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
