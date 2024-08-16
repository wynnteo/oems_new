<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student; 
use App\Models\Enrolment; 

class StudentCourseController extends Controller
{
    /**
     * Display a listing of the student's enrolled courses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the currently authenticated user
        $user = Auth::user();
        
        $student = Student::find(1);
        if (!$student) {
            return abort(404, 'Student not found');
        }

        $enrollments = Enrolment::where('student_id', $student->id)->with('course')->get();

        // Return the view with the courses and their enrollment dates
        return view('student.courses.index', compact('enrollments'));
    }
}
