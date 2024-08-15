<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index() 
    {
        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'course_code' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);

        Course::create($request->all());

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required',
            'course_code' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        $course->update($request->all());

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully');
    }

    public function show(Course $course)
    {
        $enrolments = $course->enrolments()->with('student')->get();
        $exams = $course->exams; 
        return view('admin.courses.show', compact('course', 'enrolments', 'exams'));
    }

    public function search(Request $request)
    {
        $search = $request->input('q'); // Assuming 'q' is the query parameter for the search term

        // Ensure $search is a string and properly sanitized
        if (is_string($search)) {
            $courses = Course::where('title', 'like', "%{$search}%")
                            ->orWhere('course_code', 'like', "%{$search}%")
                            ->get(['id', 'title']);
        } else {
            $courses = collect(); // Return an empty collection if search input is invalid
        }

        return response()->json($courses);
    }
}
