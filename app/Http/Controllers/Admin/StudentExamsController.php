<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\StudentExams;
use App\Models\StudentExamResults;
use Illuminate\Support\Facades\Log;

class StudentExamsController extends Controller
{
    public function index()
    {
        $studentExams = StudentExams::with(['student', 'examResult', 'exam.course'])->get();

        // Optionally, calculate statistics here if needed
        // For example, totalPass, totalFail, highestMark, lowestMark
        // You can reuse similar logic as in the show method if necessary

        return view('admin.results.index', compact('studentExams'));
    }

    public function view($id)
    {
        $result = StudentExamResults::with(['studentExam.student', 'studentExam.exam'])->findOrFail($id);
        $review = $result->review;
        return view('admin.results.view', compact('result', 'review'));
    }

}
