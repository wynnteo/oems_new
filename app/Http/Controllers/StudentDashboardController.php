<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Enrolment;
use App\Models\StudentExams;
use App\Models\StudentExamResults;
use App\Models\ExamRegistration;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index() 
    {
        $student = Auth::user(); // Assuming you're using authentication
        $student = Student::find(1);
        // Get dashboard statistics
        $enrolledCoursesCount = $student->enrollments()->count();
        $totalExamsCount = $student->studentExams()->count();
        $completedExamsCount = $student->studentExams()->where('status', 'COMPLETED')->count();
        $pendingExamsCount = ExamRegistration::where('student_id', $student->id)->where('status', 'registered')->count();
        
        // Get upcoming exams (next 5)
        $upcomingExams = ExamRegistration::where('student_id', $student->id)
            ->where('exam_registrations.status', 'registered')
            ->with(['exam.course'])
            ->whereHas('exam', function($query) {
                $query->where('start_time', '>', now());
            })
            ->join('exams', 'exam_registrations.exam_id', '=', 'exams.id')
            ->orderBy('exams.start_time', 'asc')
            ->select('exam_registrations.*')
            ->take(5)
            ->get();
                
        // Get recent wallet activities (if you have a wallet system)
        $recentActivities = collect([
            [
                'type' => 'top-up',
                'description' => 'Top-up',
                'date' => Carbon::now()->subDays(4)->format('d M g:i A'),
                'icon' => 'account_balance_wallet',
                'color' => 'success'
            ],
            [
                'type' => 'purchase',
                'description' => 'Course Purchase',
                'date' => Carbon::now()->subDays(6)->format('d M g:i A'),
                'icon' => 'shopping_cart',
                'color' => 'warning'
            ],
            [
                'type' => 'refund',
                'description' => 'Refund',
                'date' => Carbon::now()->subDays(11)->format('d M g:i A'),
                'icon' => 'money_off',
                'color' => 'info'
            ]
        ]);
        
        return view('student.dashboard', compact(
            'enrolledCoursesCount',
            'totalExamsCount', 
            'completedExamsCount',
            'pendingExamsCount',
            'upcomingExams',
            'recentActivities'
        ));
    }
    
    public function courses()
    {
        $student = Auth::user();
        $student = Student::find(1);
        $enrollments = $student->enrollments()->with('course')->orderBy('enrollment_date', 'desc')->get();
        
        return view('student.courses.index', compact('enrollments'));
    }

    public function showCourseDetails($id)
    {
        //$student = auth()->user()->student;
        $student = Student::find(1);
        // Get the enrollment record to ensure student is enrolled
        $enrollment = $student->enrollments()->where('course_id', $id)->firstOrFail();
        
        $course = $enrollment->course;
        
        // Get exams for this course
        $courseExams = $course->exams()->get();
        
        return view('student.courses.show', compact('course', 'enrollment', 'courseExams', 'student'));
    }

    
    public function exams()
    {
        $student = Auth::user();
        $student = Student::find(1);

        // Get registered/upcoming exams
        $registeredExams = ExamRegistration::where('student_id', $student->id)
            ->where('status', 'registered')
            ->with(['exam.course'])
            ->whereHas('exam', function($query) {
                $query->where('start_time', '>', now());
            })
            ->get();
        
        // Get completed exams
        $completedExams = $student->studentExams()
            ->with(['exam', 'examResult'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();
        
        return view('student.exams.index', compact('registeredExams', 'completedExams'));
    }
    
    public function profile()
    {
        $student = Auth::user();
        $student = Student::find(1);
        return view('student.profile', compact('student'));
    }
    
    public function updateProfile(Request $request)
    {
        $student = Auth::user();
        $student = Student::find(1);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
        ]);
        
        $student->update($request->only([
            'name', 'email', 'phone_number', 'date_of_birth', 'gender', 'address'
        ]));
        
        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}