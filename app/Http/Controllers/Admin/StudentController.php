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
            'name' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'status' => 'required',
            'student_code' => 'required|unique:students',
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

        // Enroll student in the selected courses
        foreach ($request->courseList as $courseId) {
            Enrolment::firstOrCreate([
                'student_id' => $student->id,
                'course_id' => $courseId,
            ], [
                'enrollment_date' => now(),  // Assuming you want to capture the date of enrollment
            ]);
        }

        // Redirect back with success message
        return redirect()->route('students.show', $student->id)
            ->with('success', 'Student enrolled in selected courses successfully!');
    }

    public function unenroll(Student $student, $enrollmentId)
    {
        // Find the enrollment record using the enrollment ID
        $enrollment = Enrolment::where('id', $enrollmentId)->where('student_id', $student->id)->first();

        if ($enrollment) {
            // Delete the enrollment record
            $enrollment->delete();
            
            // Optional: Flash message or other logic
            session()->flash('success', 'Successfully unenrolled from course.');
        } else {
            // Optional: Flash error message or other logic
            session()->flash('error', 'Enrollment record not found.');
        }

        // Redirect back to the previous page
        return redirect()->route('students.show', $student->id)
            ->with('success', 'Student unenrolled from course successfully!');
    }

}
