<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrolment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:M,F',
            'status' => 'required|in:active,inactive',
            'student_code' => 'required|string|unique:students,student_code|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        Student::create($request->all());
        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'status' => 'requred',
            'student_code' => 'required|unique:students,student_code,' .$student->student_code ,
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:M,F',
            'status' => 'required|in:active,inactive',
            'student_code' => 'required|string|unique:students,student_code,' . $student->student_code,
            'address' => 'nullable|string|max:500',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }

    public function show(Student $student)
    {
        $student->load(['enrollments.course', 'studentExams.exam', 'studentExams.examResult']);
        return view('admin.students.show', compact('student'));
    }

    public function enroll(Request $request, $id)
    {
        // Find the student by ID
        $student = Student::findOrFail($id);

        // Validate the request
        $request->validate([
            'courseList' => 'required|array',
            'courseList.*' => 'exists:courses,id',
        ]);

        $enrolledCount = 0;
        $alreadyEnrolled = [];

        // Enroll student in the selected courses
        foreach ($request->courseList as $courseId) {
            $enrollment = Enrolment::firstOrCreate([
                'student_id' => $student->id,
                'course_id' => $courseId,
            ], [
                'enrollment_date' => now(),
                'status' => 'active', // Set default status
            ]);
            
            if ($enrollment->wasRecentlyCreated) {
                $enrolledCount++;
            } else {
                $alreadyEnrolled[] = $enrollment->course->title ?? "Course ID: $courseId";
            }
        }

        $message = "Student enrolled in $enrolledCount course(s) successfully!";
        if (!empty($alreadyEnrolled)) {
            $message .= " Note: Already enrolled in: " . implode(', ', $alreadyEnrolled);
        }

        // Redirect back with success message
        return redirect()->route('students.show', $student->id)
            ->with('success', $message);
    }

    public function unenroll(Student $student, $enrollmentId)
    {
        $enrollment = Enrolment::where('id', $enrollmentId)
            ->where('student_id', $student->id)
            ->with('course')
            ->first();

        if ($enrollment) {
            $courseName = $enrollment->course->title ?? 'Unknown Course';
            
            // Delete the enrollment record
            $enrollment->delete();
            
            $message = "Successfully unenrolled from $courseName.";
        } else {
            $message = 'Enrollment record not found.';
        }

        // Check if it's an AJAX request
        if (request()->ajax()) {
            if ($enrollment) {
                return response()->json(['success' => true, 'message' => $message]);
            } else {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
        }

        // Redirect back to the previous page for non-AJAX requests
        return redirect()->route('students.show', $student->id)
            ->with($enrollment ? 'success' : 'error', $message);
    }

}
