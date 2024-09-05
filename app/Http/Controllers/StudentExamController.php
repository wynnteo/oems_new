<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Question;
use App\Models\StudentExams;
use Carbon\Carbon;

class StudentExamController extends Controller
{
    public function index()
    {
        // Get the currently authenticated student (replace with Auth::user() if authentication is set up)
        $student = Student::find(1);
        if (!$student) {
            return abort(404, 'Student not found');
        }

        // Fetch student exams
        $registeredExams = $student->studentExams()
            ->with('exam')
            ->whereNull('completed_at')
            ->get();

        $completedExams = $student->studentExams()
            ->whereNotNull('completed_at')
            ->with('exam')
            ->get();

        return view('student.exams.index', [
            'registeredExams' => $registeredExams,
            'completedExams' => $completedExams,
        ]);
    }
    public function show($examId)
    {
        $exam = Exam::findOrFail($examId);

       // $student = Auth::user();
        $student = Student::find(1);
        $error = null;

        if (!$student->courses->contains($exam->course_id)) {
            $error = 'You are not enrolled in the course for this exam.';
        }

        if ($exam->status !== 'active') {
            $error = 'This exam is not active.';
        }

        if (now()->lt($exam->start_time)) {
            $error = 'This exam is not yet available.';
        }

        return view('student.exam', compact('exam', 'error'));
    }

    public function start(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        //$student = Auth::user();
        $student = Student::find(1);
        $sessionKey = Str::uuid();

        if (!$student->courses->contains($exam->course_id)) {
            return redirect()->back()->withErrors('You are not enrolled in the course for this exam.');
        }

        if ($exam->status !== 'available') {
            return redirect()->back()->withErrors('This exam is not active.');
        }
        if (now()->lt($exam->start_time)) {
            return redirect()->back()->withErrors('This exam is not yet available.');
        }

        $studentExam = StudentExams::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('status', 'STARTED')
            ->first();

        if (!$studentExam) {
            $questions = $exam->questions;
            if ($exam->randomize_questions) {
                $questions = $questions->shuffle();
            }

            $selectedQuestions = $questions->take($exam->number_of_questions);
            $progress = $selectedQuestions->map(function ($question) {
                return [
                    'question_id' => $question->id,
                    'student_answer' => null,
                    'question_marked_review' => false,
                ];
            });

            $studentExam = StudentExams::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'session_key' => $sessionKey,
                'started_at' => now(),
                'progress' => json_encode($progress), 
                'current_question_id' => $selectedQuestions->first()->id,
            ]);

            return redirect()->route('exam.page', ['code' => $exam->id, 'session_key' => $sessionKey])
                ->with('success', 'Exam started successfully!');
        } 

        return redirect()->route('exam.page', ['code' => $exam->id, 'session_key' => $studentExam->sessionKey])
                ->with('success', 'Exam started successfully!');
    }

    public function showExamPage($code, $session_key, Request $request)
    {
        $exam = Exam::findOrFail($code);
        //$student = Auth::user();
        $student = Student::find(1);

        $studentExam = StudentExams::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('session_key', $session_key)
            ->selectRaw("*, CONVERT_TZ(started_at, '+08:00', '+00:00') as started_at_utc")
            ->firstOrFail();

        $progress = json_decode($studentExam->progress, true);
        $questionIds = collect($progress)->pluck('question_id');
        $questions = Question::whereIn('id', $questionIds)->get();

        $questionIndex = $request->input('question_index');
        if ($questionIndex !== null && is_numeric($questionIndex)) {
            $questionIndex = (int) $questionIndex;

            if ($questionIndex >= 0 && $questionIndex < count($questionIds)) {
                
                $currentIndex = $questionIndex;
                $studentExam->current_question_id = $currentIndex;
                $studentExam->save();
            } else {
                $currentIndex = $studentExam->current_question_id;
            }
        } else {
            $currentIndex = $studentExam->current_question_id;
        }

        $currentQuestionId = $questionIds[$currentIndex];
        $currentQuestion = $questions->firstWhere('id', $currentQuestionId);
        $currentQuestion->options = json_decode($currentQuestion->options, true);

        $previousQuestion = $currentIndex > 0 ? $currentIndex - 1 : null;
        $nextQuestion = $currentIndex < $questionIds->count() - 1 ? $currentIndex + 1 : null;

        $duration = $exam->duration;
        $durationUnit = $exam->duration_unit;
        $startedAt = $studentExam->started_at_utc;

        if ($durationUnit == 'hours') {
            $durationInMinutes = $duration * 60;
        } else {
            $durationInMinutes = $duration;
        }

        $currentTime = now();
        $endTime = $startedAt->copy()->addMinutes($durationInMinutes);
        $timeLeftInSeconds = $currentTime->diffInSeconds($endTime, false); 
        $timeLeftInSeconds = $timeLeftInSeconds > 0 ? $timeLeftInSeconds : 0;

        return view('student.exam.page', compact('exam', 'timeLeftInSeconds', 'questions', 'currentIndex', 'progress', 'currentQuestion', 'previousQuestion', 'nextQuestion', 'session_key'));
    }

    public function submitAnswer($examId, Request $request)
    {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'nullable|array',
            'answer.*' => 'nullable|string',
            'action' => 'required|in:next,previous',
            'question_index' => 'nullable|integer', 
        ]);

        //$student = Auth::user();
        $student = Student::find(1);
        $studentExam = StudentExams::where('exam_id', $examId)
                            ->where('student_id', $student->id)
                            ->where('session_key', $request->session_key)
                            ->firstOrFail();

        $progress = json_decode($studentExam->progress, true) ?? [];
        $progressIndex = collect($progress)->search(function ($item) use ($request) {
            return $item['question_id'] == $request->question_id;
        });

        if ($progressIndex !== false) {
            $progress[$progressIndex]['student_answer'] = $request->input('answer');
            $progress[$progressIndex]['question_marked_review'] = $request->has('question_marked_review');
        }

        $studentExam->progress = json_encode($progress);

        if ($request->input('action') === 'submit') {
            // Handle exam submission logic here
            $studentExam->status = 'COMPLETED'; // Update the status to 'COMPLETED'
            $studentExam->completed_at = now(); // Set the completion time
        } else {
            if ($request->filled('question_index')) {
                $studentExam->current_question_id = $request->input('question_index');
            } else {
                if ($request->input('action') === 'next') {
                    $studentExam->current_question_id = min($studentExam->current_question_id + 1, count($progress) - 1);
                } else {
                    $studentExam->current_question_id = max($studentExam->current_question_id - 1, 0);
                }
            }
        }
    
        $studentExam->save();
        $redirectRoute = $request->input('action') === 'submit' ? 'exam.summary' : 'exam.page';

        return redirect()->route($redirectRoute, ['code' => $examId, 'session_key' => $request->session_key])
                         ->with('success', 'Answer saved successfully!');
    }

    public function showAfterExamCompleted($code, $session_key, Request $request)
    {
        $exam = Exam::findOrFail($code);
        //$student = Auth::user();
        $student = Student::find(1);

        $studentExam = StudentExams::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('session_key', $session_key)
            ->firstOrFail();

        // Assuming you have a way to get the results, calculate score, etc.
        $results = $this->calculateResults($studentExam);

        return view('student.exam.summary', [
            'exam' => $exam,
            'examStatus' => $studentExam->status,
            'totalQuestions' => $results['totalQuestions'],
            'correctAnswers' => $results['correctAnswers'],
            'incorrectAnswers' => $results['incorrectAnswers'],
            'score' => $results['score'],
            'passFailStatus' => $results['passFailStatus'],
            'showReview' => $request->input('review', false), // Optionally control if review section should be shown
            'questions' => $results['questions'] // List of questions for review
        ]);
    }

    private function calculateResults($studentExam)
    {
        // Implement logic to calculate totalQuestions, correctAnswers, incorrectAnswers, score, passFailStatus
        // You might need to access $studentExam->progress or other related models

        return [
            'totalQuestions' => 20, // Example value
            'correctAnswers' => 15, // Example value
            'incorrectAnswers' => 5, // Example value
            'score' => 75, // Example value
            'passFailStatus' => 'Passed', // Example value
            'questions' => [] // Example list of questions
        ];
    }

}
