<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;
use App\Models\ExamRegistration;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamScheduleController extends Controller
{
    /**
     * Display available exams for scheduling
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get student's enrolled courses
        $enrolledCourses = Course::whereHas('enrollments', function($query) use ($student) {
            $query->where('student_id', $student->id)
                  ->whereIn('status', ['active', 'enrolled']);
        })->with('enrollments')->get();

        // Get available exams for enrolled courses
        $query = Exam::whereIn('course_id', $enrolledCourses->pluck('id'))
            ->where('status', 'active')
            ->where('start_time', '>', now())
            ->with(['course', 'registrations' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }]);

        // Apply filters
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('date') && $request->date) {
            $date = Carbon::parse($request->date);
            $query->whereDate('start_time', $date);
        }

        $availableExams = $query->orderBy('start_time')->get();

        // Add registration counts and check if student is already registered
        $availableExams = $availableExams->map(function($exam) use ($student) {
            $exam->registrations_count = ExamRegistration::where('exam_id', $exam->id)->count();
            $exam->is_registered = ExamRegistration::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->exists();
            return $exam;
        });

        return view('student.exams.schedule', compact('enrolledCourses', 'availableExams'));
    }

    /**
     * Get exam details for scheduling modal
     */
    public function getExamDetails($examId)
    {
        $exam = Exam::with('course')->findOrFail($examId);
        
        // Check if student is enrolled in the course
        $student = Auth::user();
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $exam->course_id)
            ->whereIn('status', ['active', 'enrolled'])
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['error' => 'You are not enrolled in this course'], 403);
        }

        return response()->json([
            'id' => $exam->id,
            'title' => $exam->title,
            'exam_code' => $exam->exam_code,
            'course' => [
                'id' => $exam->course->id,
                'title' => $exam->course->title,
                'course_code' => $exam->course->course_code ?? ''
            ],
            'formatted_date' => Carbon::parse($exam->start_time)->format('M d, Y'),
            'formatted_time' => Carbon::parse($exam->start_time)->format('g:i A') . ' - ' . Carbon::parse($exam->end_time)->format('g:i A'),
            'duration' => $exam->duration,
            'duration_unit' => $exam->duration_unit ?? 'minutes'
        ]);
    }

    /**
     * Get full exam details for details modal
     */
    public function getFullExamDetails($examId)
    {
        $exam = Exam::with('course')->findOrFail($examId);
        
        // Check if student is enrolled in the course
        $student = Auth::user();
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $exam->course_id)
            ->whereIn('status', ['active', 'enrolled'])
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['error' => 'You are not enrolled in this course'], 403);
        }

        return response()->json([
            'id' => $exam->id,
            'title' => $exam->title,
            'exam_code' => $exam->exam_code,
            'description' => $exam->description,
            'course' => [
                'id' => $exam->course->id,
                'title' => $exam->course->title,
                'course_code' => $exam->course->course_code ?? ''
            ],
            'formatted_date' => Carbon::parse($exam->start_time)->format('M d, Y'),
            'formatted_time' => Carbon::parse($exam->start_time)->format('g:i A') . ' - ' . Carbon::parse($exam->end_time)->format('g:i A'),
            'duration' => $exam->duration,
            'duration_unit' => $exam->duration_unit ?? 'minutes',
            'number_of_questions' => $exam->number_of_questions,
            'passing_grade' => $exam->passing_grade,
            'retake_allowed' => $exam->retake_allowed,
            'review_questions' => $exam->review_questions,
            'access_code' => $exam->access_code ? true : false
        ]);
    }

    /**
     * Schedule an exam (register student for exam)
     */
    public function scheduleExam(Request $request, $examId)
    {
        try {
            DB::beginTransaction();

            $student = Auth::user();
            $exam = Exam::findOrFail($examId);

            // Validation checks
            $validationResult = $this->validateExamScheduling($student, $exam);
            if ($validationResult !== true) {
                return response()->json(['success' => false, 'message' => $validationResult], 400);
            }

            // Check if already registered
            $existingRegistration = ExamRegistration::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->first();

            if ($existingRegistration) {
                return response()->json(['success' => false, 'message' => 'You are already registered for this exam'], 400);
            }

            // Check capacity (if applicable)
            $capacity = $exam->capacity ?? 50; // Default capacity
            $currentRegistrations = ExamRegistration::where('exam_id', $examId)->count();

            if ($currentRegistrations >= $capacity) {
                return response()->json(['success' => false, 'message' => 'This exam session is full'], 400);
            }

            // Create registration
            ExamRegistration::create([
                'exam_id' => $examId,
                'student_id' => $student->id,
                'registered_at' => now(),
                'status' => 'registered'
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Successfully registered for the exam!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while registering for the exam'
            ], 500);
        }
    }

    /**
     * Cancel exam registration
     */
    public function cancelExamRegistration($examId)
    {
        try {
            $student = Auth::user();
            $exam = Exam::findOrFail($examId);

            // Check if registration exists
            $registration = ExamRegistration::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'You are not registered for this exam'], 400);
            }

            // Check if cancellation is still allowed (e.g., not within 24 hours of exam)
            $startTime = Carbon::parse($exam->start_time);
            $cancellationDeadline = $startTime->subHours(24);

            if (now()->isAfter($cancellationDeadline)) {
                return response()->json(['success' => false, 'message' => 'Cancellation deadline has passed'], 400);
            }

            // Delete registration
            $registration->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Registration cancelled successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while cancelling the registration'
            ], 500);
        }
    }
}