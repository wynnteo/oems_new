<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;
use App\Models\StudentExams;
use App\Models\StudentExamResults;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('ratings')->get();
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
            'pagination' => $request->has('pagination'),
            'ip_restrictions' => $request->has('ip_restrictions'),
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'exam_code' => 'required|string|unique:exams,exam_code',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'duration_unit' => 'required|string|in:minutes,hours',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'number_of_questions' => 'nullable|integer|min:1',
            'randomize_questions' => 'nullable|boolean',
            'retake_allowed' => 'nullable|boolean',
            'number_retake' => 'nullable|integer|min:0',
            'passing_grade' => 'nullable|numeric|between:0,100',
            'review_questions' => 'nullable|boolean',
            'show_answers' => 'nullable|boolean',
            'pagination' => 'nullable|boolean',
            'status' => 'required|string|in:available,not_available',
            'access_code' => 'nullable|string|max:255',
            'allow_rating' => 'nullable|boolean',
            'ip_restrictions' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0',
            'course_id' => 'required|exists:courses,id',
        ]);

        Exam::create([
            'title' => $request->input('title'),
            'exam_code' => $request->input('exam_code'),
            'description' => $request->input('description'),
            'duration' => $request->input('duration'),
            'duration_unit' => $request->input('duration_unit'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'number_of_questions' => $request->input('number_of_questions'),
            'randomize_questions' => $request->input('randomize_questions'),
            'retake_allowed' => $request->input('retake_allowed'),
            'number_retake' => $request->input('number_retake'),
            'passing_grade' => $request->input('passing_grade'),
            'review_questions' => $request->input('review_questions'),
            'show_answers' => $request->input('show_answers'),
            'pagination' => $request->input('pagination'),
            'status' => $request->input('status'),
            'access_code' => $request->input('access_code'),
            'allow_rating' => $request->input('allow_rating'),
            'ip_restrictions' => $request->input('ip_restrictions'),
            'price' => $request->input('price'),
            'course_id' => $request->input('course_id'),
        ]);

        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    // Display the specified exam
    public function show(Exam $exam)
    {
        try {
            // Eager load studentExams with related student and examResult
            $studentExams = $exam->studentExams()->with(['student', 'examResult'])->get();

            Log::info('Student Exams Data:', ['count' => $studentExams->count(), 'exam_id' => $exam->id]);

            // Initialize counters
            $totalAttempts = $studentExams->count();
            $totalPass = 0;
            $totalFail = 0;
            $scores = [];

            foreach ($studentExams as $studentExam) {
                $result = $studentExam->examResult;
                
                if ($result && $result->score !== null) {
                    $scores[] = $result->score;
                    
                    if ($result->score >= ($exam->passing_grade ?? 0)) {
                        $totalPass++;
                    } else {
                        $totalFail++;
                    }
                } else {
                    // Count as fail if no result or no score
                    $totalFail++;
                }
            }

            // Calculate statistics
            $highestMark = !empty($scores) ? max($scores) : null;
            $lowestMark = !empty($scores) ? min($scores) : null;
            $averageMark = !empty($scores) ? round(array_sum($scores) / count($scores), 1) : null;

            // Calculate pass rate
            $passRate = $totalAttempts > 0 ? round(($totalPass / $totalAttempts) * 100, 1) : 0;

            // Additional statistics
            $completedAttempts = $studentExams->filter(function ($se) {
                return $se->completed_at !== null;
            })->count();

            $inProgressAttempts = $totalAttempts - $completedAttempts;

            // Get question count for the exam
            $totalQuestions = $exam->questions()->count();

            Log::info('Exam Statistics:', [
                'exam_id' => $exam->id,
                'total_attempts' => $totalAttempts,
                'total_pass' => $totalPass,
                'total_fail' => $totalFail,
                'pass_rate' => $passRate,
                'highest_mark' => $highestMark,
                'lowest_mark' => $lowestMark,
                'average_mark' => $averageMark,
                'completed_attempts' => $completedAttempts,
                'in_progress_attempts' => $inProgressAttempts,
                'total_questions' => $totalQuestions
            ]);

            $ratings = $exam->ratings()->with('student')->get();
            $totalRatings = $ratings->count();
            $averageRating = $totalRatings > 0 ? round($ratings->avg('rating'), 1) : null;

            // Pass all data to the view
            return view('admin.exams.show', compact(
                'exam', 
                'totalPass', 
                'totalFail', 
                'totalAttempts',
                'passRate',
                'highestMark', 
                'lowestMark', 
                'averageMark',
                'completedAttempts',
                'inProgressAttempts',
                'totalQuestions',
                'studentExams',
                'ratings', 
                'totalRatings',  
                'averageRating'
            ));

        } catch (\Exception $e) {
            Log::error('Error in ExamController@show:', [
                'exam_id' => $exam->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('exams.index')
                ->with('error', 'Unable to load exam details. Please try again.');
        }
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
            'pagination' => $request->has('pagination'),
            'ip_restrictions' => $request->has('ip_restrictions'),
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'exam_code' => 'required|string|unique:exams,exam_code,' . $exam->id,
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'duration_unit' => 'required|string|in:minutes,hours',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'number_of_questions' => 'nullable|integer|min:1',
            'randomize_questions' => 'nullable|boolean',
            'retake_allowed' => 'nullable|boolean',
            'number_retake' => 'nullable|integer|min:0',
            'passing_grade' => 'nullable|numeric|between:0,100',
            'review_questions' => 'nullable|boolean',
            'show_answers' => 'nullable|boolean',
            'pagination' => 'nullable|boolean',
            'status' => 'required|string|in:available,not_available',
            'access_code' => 'nullable|string|max:255',
            'allow_rating' => 'nullable|boolean',
            'ip_restrictions' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0',
            'course_id' => 'required|exists:courses,id',
        ]);

        $exam->update($request->all());
        return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
    }

    // Remove the specified exam from storage
    public function destroy(Exam $exam)
    {
        try {
            $exam->delete();
            return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting exam:', [
                'exam_id' => $exam->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('exams.index')->with('error', 'Unable to delete exam. Please try again.');
        }
    }

    public function toggleStatus(Request $request, Exam $exam)
    {
        try {
            $exam->update(['status' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Exam status updated successfully.'
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => 'Unable to update exam status.'
            ], 500);
        }
    }
}