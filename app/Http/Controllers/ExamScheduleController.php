<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Student;
use App\Models\ExamRegistration;
use App\Models\Enrolment;
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
        $student = Student::find(1);
        // Get student's enrolled courses
        $enrolledCourses = Course::whereHas('enrolments', function($query) use ($student) {
            $query->where('student_id', $student->id);
        })->with('enrolments')->get();

        // Get available exams for enrolled courses
        $query = Exam::whereIn('course_id', $enrolledCourses->pluck('id'))
            ->where('status', 'available')
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
        $student = Student::find(1);
        $isEnrolled = Enrolment::where('student_id', $student->id)
            ->where('course_id', $exam->course_id)
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
        $student = Student::find(1);
        $isEnrolled = Enrolment::where('student_id', $student->id)
            ->where('course_id', $exam->course_id)
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
            $student = Student::find(1);
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
     * Cancel exam registration with reason
     */
    public function cancelExamRegistration(Request $request, $examId)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500'
        ]);

        try {
            $student = Auth::user();
            $student = Student::find(1);
            $exam = Exam::findOrFail($examId);

            // Check if registration exists
            $registration = ExamRegistration::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'You are not registered for this exam'], 400);
            }

            // Check if cancellation is still allowed (24 hours before exam)
            $startTime = Carbon::parse($exam->start_time);
            $cancellationDeadline = $startTime->copy()->subHours(24);

            if (now()->isAfter($cancellationDeadline)) {
                return response()->json(['success' => false, 'message' => 'Cancellation deadline has passed (24 hours before exam)'], 400);
            }

            // Update registration with cancellation info
            $registration->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now()
            ]);

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

    /**
     * Get student's scheduled exams
     */
    public function getScheduledExams()
    {
        $student = Auth::user();
        $student = Student::find(1);
        $scheduledExams = ExamRegistration::with(['exam.course'])
            ->where('student_id', $student->id)
            ->where('status', 'registered')
            ->whereHas('exam', function($query) {
                $query->where('start_time', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $scheduledExams->map(function($registration) {
                return [
                    'id' => $registration->id,
                    'exam' => [
                        'id' => $registration->exam->id,
                        'title' => $registration->exam->title,
                        'exam_code' => $registration->exam->exam_code,
                        'start_time' => $registration->exam->start_time,
                        'end_time' => $registration->exam->end_time,
                        'duration' => $registration->exam->duration,
                        'course' => [
                            'title' => $registration->exam->course->title,
                            'course_code' => $registration->exam->course->course_code
                        ]
                    ],
                    'registered_at' => $registration->registered_at,
                    'status' => $registration->status
                ];
            })
        ]);
    }

    /**
     * Validate exam scheduling requirements
     */
    private function validateExamScheduling($student, $exam)
    {
        // Check if student is enrolled in the course
        $isEnrolled = Enrolment::where('student_id', $student->id)
            ->where('course_id', $exam->course_id)
            ->exists();

        if (!$isEnrolled) {
            return 'You are not enrolled in this course';
        }

        // Check if exam is active
        if ($exam->status !== 'available') {
            return 'This exam is not available for registration';
        }

        // Check if exam is in the future
        if (Carbon::parse($exam->start_time)->isPast()) {
            return 'This exam has already started or finished';
        }

        // Check if registration is still open (2 hours before exam)
        $registrationDeadline = Carbon::parse($exam->start_time)->subHours(2);
        if (now()->isAfter($registrationDeadline)) {
            return 'Registration deadline has passed';
        }

        // Check for schedule conflicts
        $conflictingExam = ExamRegistration::where('student_id', $student->id)
            ->where('status', 'registered')
            ->whereHas('exam', function($query) use ($exam) {
                $query->where(function($q) use ($exam) {
                    // Check for overlapping times
                    $q->whereBetween('start_time', [$exam->start_time, $exam->end_time])
                      ->orWhereBetween('end_time', [$exam->start_time, $exam->end_time])
                      ->orWhere(function($innerQ) use ($exam) {
                          $innerQ->where('start_time', '<=', $exam->start_time)
                                 ->where('end_time', '>=', $exam->end_time);
                      });
                });
            })
            ->exists();

        if ($conflictingExam) {
            return 'You have a conflicting exam scheduled at the same time';
        }

        // Check if student has already taken this exam (if retakes not allowed)
        if (!$exam->retake_allowed) {
            $hasCompleted = ExamRegistration::where('student_id', $student->id)
                ->where('exam_id', $exam->id)
                ->where('status', 'completed')
                ->exists();

            if ($hasCompleted) {
                return 'You have already completed this exam and retakes are not allowed';
            }
        }

        return true;
    }

    /**
     * Check if student can take the exam now
     */
    public function checkExamAvailability($examId)
    {
        try {
            $student = Auth::user();
            $student = Student::find(1);
            $exam = Exam::findOrFail($examId);

            // Check if student is registered for this exam
            $registration = ExamRegistration::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->where('status', 'registered')
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not registered for this exam'
                ], 403);
            }

            $startTime = Carbon::parse($exam->start_time);
            $endTime = Carbon::parse($exam->end_time);
            $now = now();

            // Check if exam is available (15 minutes before to 15 minutes after start time)
            $allowedStartTime = $startTime->copy()->subMinutes(15);
            $lateEntryDeadline = $startTime->copy()->addMinutes(15);

            $canStart = $now->isBetween($allowedStartTime, $lateEntryDeadline);
            $hasEnded = $now->isAfter($endTime);

            if ($hasEnded) {
                return response()->json([
                    'success' => false,
                    'message' => 'This exam has ended'
                ], 400);
            }

            if (!$canStart) {
                $timeUntilStart = $allowedStartTime->diffForHumans();
                return response()->json([
                    'success' => false,
                    'message' => "Exam will be available {$timeUntilStart}"
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Exam is available',
                'data' => [
                    'exam_id' => $exam->id,
                    'time_remaining' => $endTime->diffInMinutes($now),
                    'can_start' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking exam availability'
            ], 500);
        }
    }
    /**
     * Show exam details page
     */
    public function showExamDetails($examId)
    {
        $student = Auth::user();
        $student = Student::find(1);
        
        $exam = Exam::with(['course', 'registrations' => function($query) use ($student) {
            $query->where('student_id', $student->id);
        }])->findOrFail($examId);

        // Check if student is enrolled in the course
        $isEnrolled = Enrolment::where('student_id', $student->id)
            ->where('course_id', $exam->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.exams')->with('error', 'You are not enrolled in this course');
        }

        // Check if student is registered
        $registration = ExamRegistration::where('exam_id', $examId)
            ->where('student_id', $student->id)
            ->first();

        // Get registration statistics
        $totalRegistrations = ExamRegistration::where('exam_id', $examId)->count();
        $capacity = $exam->capacity ?? 50;

        return view('student.exams.details', compact('exam', 'registration', 'totalRegistrations', 'capacity'));
    }

    /**
     * Process exam reschedule
     */
    public function rescheduleExam(Request $request, $examId)
    {
        $request->validate([
            'new_exam_id' => 'required|exists:exams,id',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $student = Auth::user();
            $student = Student::find(1);
            
            $currentExam = Exam::findOrFail($examId);
            $newExam = Exam::findOrFail($request->new_exam_id);

            // Validate reschedule conditions
            $validationResult = $this->validateReschedule($student, $currentExam, $newExam);
            if ($validationResult !== true) {
                return redirect()->back()->with('error', $validationResult);
            }

            // Update registration
            $registration = ExamRegistration::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->first();

            $registration->update([
                'exam_id' => $request->new_exam_id,
                'reschedule_reason' => $request->reason,
                'rescheduled_at' => now()
            ]);

            DB::commit();

            return redirect()->route('student.exams')->with('success', 'Exam rescheduled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while rescheduling the exam');
        }
    }

    /**
     * Validate reschedule conditions
     */
    private function validateReschedule($student, $currentExam, $newExam)
    {
        // Check if new exam is for the same course
        if ($currentExam->course_id !== $newExam->course_id) {
            return 'Can only reschedule to exams of the same course';
        }

        // Check if new exam is available
        if ($newExam->status !== 'available') {
            return 'Selected exam is not available';
        }

        // Check capacity
        $capacity = $newExam->capacity ?? 50;
        $currentRegistrations = ExamRegistration::where('exam_id', $newExam->id)->count();

        if ($currentRegistrations >= $capacity) {
            return 'Selected exam session is full';
        }

        // Check for schedule conflicts
        $conflictingExam = ExamRegistration::where('student_id', $student->id)
            ->where('exam_id', '!=', $currentExam->id)
            ->where('status', 'registered')
            ->whereHas('exam', function($query) use ($newExam) {
                $query->where(function($q) use ($newExam) {
                    $q->whereBetween('start_time', [$newExam->start_time, $newExam->end_time])
                      ->orWhereBetween('end_time', [$newExam->start_time, $newExam->end_time])
                      ->orWhere(function($innerQ) use ($newExam) {
                          $innerQ->where('start_time', '<=', $newExam->start_time)
                                 ->where('end_time', '>=', $newExam->end_time);
                      });
                });
            })
            ->exists();

        if ($conflictingExam) {
            return 'You have a conflicting exam scheduled at the same time';
        }

        return true;
    }

    /**
     * Show reschedule form
     */
    public function showRescheduleForm($examId)
    {
        $student = Auth::user();
        $student = Student::find(1);
        
        $exam = Exam::with('course')->findOrFail($examId);
        
        // Check if student is registered
        $registration = ExamRegistration::where('exam_id', $examId)
            ->where('student_id', $student->id)
            ->where('status', 'registered')
            ->first();

        if (!$registration) {
            return redirect()->route('student.exams')->with('error', 'You are not registered for this exam');
        }

        // Check if reschedule is allowed (not within 48 hours)
        $startTime = Carbon::parse($exam->start_time);
        $rescheduleDeadline = $startTime->copy()->subHours(48);

        if (now()->isAfter($rescheduleDeadline)) {
            return redirect()->route('student.exams')->with('error', 'Reschedule deadline has passed (48 hours before exam)');
        }

        // Get alternative exam dates for the same course
        $alternativeExams = Exam::where('course_id', $exam->course_id)
            ->where('id', '!=', $examId)
            ->where('status', 'available')
            ->where('start_time', '>', now()->addHours(48))
            ->whereDoesntHave('registrations', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->orderBy('start_time')
            ->get();

        return view('student.exams.reschedule', compact('exam', 'registration', 'alternativeExams'));
    }

}