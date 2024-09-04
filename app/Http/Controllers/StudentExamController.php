<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Question;
use App\Models\StudentExams;

class StudentExamController extends Controller
{
    public function show($examId)
    {
        // Fetch the exam details using the unique ID
        $exam = Exam::findOrFail($examId);

       // $student = Auth::user();
        $student = Student::find(1);
        // Initialize an empty error message
        $error = null;

        // Check if the student is enrolled in the course associated with the exam
        if (!$student->courses->contains($exam->course_id)) {
            $error = 'You are not enrolled in the course for this exam.';
        }

        // Check if the exam is active
        if ($exam->status !== 'active') {
            $error = 'This exam is not active.';
        }

        // Check if the current time is after the exam's start time
        if (now()->lt($exam->start_time)) {
            $error = 'This exam is not yet available.';
        }

        // Pass the exam data and the error message to the view
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

        // Check if the student has already started the exam
        $studentExam = StudentExams::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('status', 'STARTED')
            ->first();

        if (!$studentExam) {
            // Randomize and select questions if needed
            $questions = $exam->questions;
            if ($exam->randomize_questions) {
                $questions = $questions->shuffle();
            }

            // Select the required number of questions
            $selectedQuestions = $questions->take($exam->number_of_questions);

            // Store the question IDs and the selected questions in the progress
            $progress = $selectedQuestions->map(function ($question) {
                return [
                    'question_id' => $question->id,
                    'student_answer' => null,
                    'question_marked_review' => false,
                ];
            });

            // Initialize the student exam record
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
        Log::info('Showing exam page', ['code' => $code, 'session_key' => $session_key]);
        //$student = Auth::user();
        $student = Student::find(1);

        // Retrieve the specific session for this exam
        $studentExam = StudentExams::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('session_key', $session_key)
            ->firstOrFail();

        $progress = json_decode($studentExam->progress, true);
        $questionIds = collect($progress)->pluck('question_id');
        $questions = Question::whereIn('id', $questionIds)->get();

        // Check if question_index is provided
        $questionIndex = $request->input('question_index');
        if ($questionIndex !== null && is_numeric($questionIndex)) {
            $questionIndex = (int) $questionIndex;

            // Ensure the index is within bounds
            if ($questionIndex >= 0 && $questionIndex < count($questionIds)) {
                
                $currentIndex = $questionIndex;
                $studentExam->current_question_id = $currentIndex; // Update current question
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

        Log::info($currentQuestion);
        Log::info('Showing exam page', ['currentIndex' => $currentIndex, 'previousQuestion' => $previousQuestion, 'nextQuestion' => $nextQuestion]);
        return view('student.exam.page', compact('exam', 'questions', 'currentIndex', 'progress', 'currentQuestion', 'previousQuestion', 'nextQuestion', 'session_key'));
    }

    public function submitAnswer($examId, Request $request)
    {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'nullable|array',
            'answer.*' => 'nullable|string',
            'question_marked_review' => 'nullable|boolean',
            'action' => 'required|in:next,previous',
            'question_index' => 'nullable|integer', 
        ]);

        //$student = Auth::user();
        $student = Student::find(1);
        $studentExam = StudentExams::where('exam_id', $examId)
                            ->where('student_id', $student->id)
                            ->where('session_key', $request->session_key)
                            ->firstOrFail();

        // Update progress
        $progress = json_decode($studentExam->progress, true) ?? [];
        $progressIndex = collect($progress)->search(function ($item) use ($request) {
            return $item['question_id'] == $request->question_id;
        });

        if ($progressIndex !== false) {
            // Update the existing progress item
            $progress[$progressIndex]['student_answer'] = $request->input('answer');
            $progress[$progressIndex]['question_marked_review'] = $request->input('question_marked_review', false);
        }

        $studentExam->progress = json_encode($progress);

        // Determine the next or previous question index based on action
        if ($request->filled('question_index')) {
            // If a specific question index is provided (for jumping directly to a question)
            $studentExam->current_question_id = $request->input('question_index');
        } else {
            // Navigate to the next or previous question
            if ($request->input('action') === 'next') {
                $studentExam->current_question_id = min($studentExam->current_question_id + 1, count($progress) - 1);
            } else {
                $studentExam->current_question_id = max($studentExam->current_question_id - 1, 0);
            }
        }

        // Save the updated studentExam record
        $studentExam->save();

        return redirect()->route('exam.page', ['code' => $examId, 'session_key' => $request->session_key])
                         ->with('success', 'Answer saved successfully!');
    }

}
