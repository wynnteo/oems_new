<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Question;
use stdClass;

class DashboardController extends Controller
{
    public function index() 
    {
        $totalCourses = Course::count();
        $totalStudents = Student::count();
        $totalExams = Exam::count();
        $totalQuestions = Question::count();

        return view('admin.dashboard', compact(
            'totalCourses', 
            'totalStudents', 
            'totalExams', 
            'totalQuestions',
        ));
    }
}