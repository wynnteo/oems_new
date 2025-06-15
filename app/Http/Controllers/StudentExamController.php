<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Question;
use App\Models\StudentExams;
use App\Models\StudentExamResults;
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

        if ($exam->status !== 'available') {
            $error = 'This Exam is Inactive.';
        }

        // if (now()->lt($exam->start_time)) {
        //     $error = 'This Exam is Unavailable.';
        // }

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
            return redirect()->back()->withErrors('This Exam is Inactive.');
        }
        if (now()->lt($exam->start_time)) {
            return redirect()->back()->withErrors('This Exam is Unavailable.');
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
                'started_at' => now('UTC'),
                'progress' => json_encode($progress), 
                'current_question_id' => 0,
                'ip_address' => $request->ip(),
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
            //->selectRaw("*, CONVERT_TZ(started_at, '+08:00', '+00:00') as started_at_utc")
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
        $startedAt = $studentExam->started_at;

        if ($durationUnit == 'hours') {
            $durationInMinutes = $duration * 60;
        } else {
            $durationInMinutes = $duration;
        }

        $currentTime = now();
        $endTime = $startedAt->copy()->addMinutes($durationInMinutes);
        $timeLeftInSeconds = $currentTime->diffInSeconds($endTime, false); 
        $timeLeftInSeconds = $timeLeftInSeconds > 0 ? $timeLeftInSeconds : 0;   
        echo $currentIndex;
        return view('student.exam.page', compact('exam', 'timeLeftInSeconds', 'questions', 'currentIndex', 'progress', 'currentQuestion', 'previousQuestion', 'nextQuestion', 'session_key'));
    }

    public function submitAnswer($examId, Request $request)
    {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'nullable|array',
            'answer.*' => 'nullable|string',
            'action' => 'required|in:next,previous,submit',
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
            $totalCorrect = 0;
            $totalQuestions = count($progress);
            $passMark = $studentExam->exam->passing_grade; 
            $studentPassed = false; 

            foreach ($progress as &$entry) {
                $question = Question::find($entry['question_id']);
                $correctAnswer = json_decode($question->correct_answer, true);
                $options = json_decode($question->options, true);
                
                if ($question->question_type === 'fill_in_the_blank_text') {
                    $isCorrect = $this->checkFillInTheBlankAnswer($entry['student_answer'], $correctAnswer);
                } else {
                    $isCorrect = $this->checkAnswer($entry['student_answer'], $correctAnswer, $options, $question->question_type);
                }
                $entry['result'] = $isCorrect ? 'correct' : 'incorrect';
                $entry['correct_answer'] = $correctAnswer;
    
                if ($isCorrect) {
                    $totalCorrect++;
                }
            }

            $passPercentage = ($totalCorrect / $totalQuestions) * 100;
            $studentPassed = $passPercentage >= $passMark;
            $studentExam->status = 'COMPLETED';
            $studentExam->completed_at = now('UTC');

            StudentExamResults::create([
                'student_exam_id' => $studentExam->id,
                'score' => $passPercentage,
                'total_correct' => $totalCorrect,
                'total_incorrect' => ($totalQuestions - $totalCorrect),
                'review' => $this->generateReview($progress, $studentExam), // Generate detailed review info
            ]);
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

    private function checkAnswer($studentAnswer, $correctAnswer, $options, $questionType)
    {
        // Handle true/false questions
        if ($questionType === 'true_false') {
            if (!$studentAnswer || empty($studentAnswer)) {
                return false;
            }
            
            $studentAnswerValue = strtolower(trim($studentAnswer[0]));
            $correctAnswerValue = strtolower(trim($correctAnswer[0]));
            
            return $studentAnswerValue === $correctAnswerValue;
        }

        // Handle other question types with options
        if (is_array($studentAnswer) && !empty($options)) {
            $studentAnswerIndexes = array_map(function($answer) use ($options) {
                return array_search($answer, $options);
            }, $studentAnswer);

            // Remove false values (not found options)
            $studentAnswerIndexes = array_filter($studentAnswerIndexes, function($index) {
                return $index !== false;
            });

            sort($studentAnswerIndexes);
            sort($correctAnswer);

            return $studentAnswerIndexes == $correctAnswer;
        }

        return false;
    }

    private function checkFillInTheBlankAnswer($studentAnswer, $correctAnswer)
    {
        if (count($studentAnswer) !== count($correctAnswer)) {
            return false;
        }
        foreach ($studentAnswer as $index => $studentBlankAnswer) {
            $acceptableAnswers = $correctAnswer[$index];
            $isCorrect = collect($acceptableAnswers)->contains(function($acceptableAnswer) use ($studentBlankAnswer) {
                return strtolower(trim($acceptableAnswer)) === strtolower(trim($studentBlankAnswer));
            });

            if (!$isCorrect) {
                return false;
            }
        }

        return true;
    }

    public function showAfterExamCompleted($code, $session_key, Request $request)
    {
        $exam = Exam::findOrFail($code);
        //$student = Auth::user();
        $student = Student::find(1);

        $studentExam = StudentExams::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('session_key', $session_key)
            ->latest()
            ->firstOrFail();
        
        $studentExamResult = StudentExamResults::where('student_exam_id', $studentExam->id)
            ->latest()
            ->firstOrFail();

        $passMark = $studentExam->exam->passing_grade; 
        $studentPassed = ($studentExamResult->score >= $passMark) ? 'Passed' : 'Failed';

        return view('student.exam.summary', [
            'exam' => $exam,
            'student' => $student,
            'studentExam' => $studentExam,
            'examStatus' => $studentExam->status,
            'totalQuestions' => ($studentExamResult->total_correct + $studentExamResult->total_incorrect),
            'correctAnswers' => $studentExamResult->total_correct,
            'incorrectAnswers' => $studentExamResult->total_incorrect,
            'score' => $studentExamResult->score,
            'passFailStatus' => $studentPassed,
            'showReview' => true,
            'review' => $studentExamResult->review,
        ]);
    }

    public function submitFeedback(Request $request, $code, $session_key)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $student = Student::find(1);
        $studentExam = StudentExams::where('exam_id', $code)
            ->where('student_id', $student->id)
            ->where('session_key', $session_key)
            ->firstOrFail();

        $studentExam->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    private function generateReview($progress, $studentExam)
    {
        $review = [];

        foreach ($progress as $entry) {
            $question = Question::find($entry['question_id']);
            $review[] = (object) [
                'question_id' => $entry['question_id'],
                'student_answer' => $entry['student_answer'],
                'correct_answer' => $entry['correct_answer'],
                'result' => $entry['result'],
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'options' => json_decode($question->options),
                'description' => $question->description,
                'image_name' => $question->image_name,
            ];
        }

        return $review;
    }

}
