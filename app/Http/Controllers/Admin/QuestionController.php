<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Exam;
use App\Imports\QuestionsImport;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class QuestionController extends Controller
{
    public function index(Request $request) 
    {
        $query = Question::with('exam');
        
        // Filter by exam if specified
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        // Filter by question type if specified
        if ($request->filled('question_type')) {
            $query->where('question_type', $request->question_type);
        }
        
        // Filter by active status if specified
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_text', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('exam', function($examQuery) use ($search) {
                      $examQuery->where('title', 'like', '%' . $search . '%')
                               ->orWhere('exam_code', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $questions = $query->latest()->get();
        $exams = Exam::orderBy('title')->get();
        
        // Get statistics for dashboard
        $stats = [
            'total' => Question::count(),
            'active' => Question::where('is_active', true)->count(),
            'inactive' => Question::where('is_active', false)->count(),
            'by_type' => Question::selectRaw('question_type, COUNT(*) as count')
                              ->groupBy('question_type')
                              ->pluck('count', 'question_type')
                              ->toArray()
        ];
        
        return view('admin.questions.index', compact('questions', 'exams', 'stats'));
    }

    public function create($examId = null)
    {
        $exams = Exam::orderBy('title')->get();
        return view('admin.questions.create', compact('exams', 'examId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank_text,fill_in_the_blank_choice,matching',
            'question_text' => 'required|string|max:5000',
            'description' => 'nullable|string|max:2000',
            'explanation' => 'nullable|string|max:2000',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:1000',
            'correct_answer' => 'required',
            'exam_id' => 'required|exists:exams,id',
            'is_active' => 'boolean',
            'difficulty_level' => 'nullable|string|in:easy,medium,hard',
            'points' => 'nullable|integer|min:1|max:100',
            'time_limit' => 'nullable|integer|min:1|max:3600', // seconds
        ]);

        $imagePath = null;
        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imagePath = $image->store('questions/images', 'public');
        }

        $correctAnswer = $this->normalizeCorrectAnswer($request->input('correct_answer'));

        $question = Question::create([
            'question_type' => $request->input('question_type'),
            'question_text' => $request->input('question_text'),
            'description' => $request->input('description'),
            'explanation' => $request->input('explanation'),
            'image_name' => $imagePath,
            'options' => json_encode($request->input('options', [])),
            'correct_answer' => $correctAnswer,
            'exam_id' => $request->input('exam_id'),
            'is_active' => $request->boolean('is_active', true),
            'difficulty_level' => $request->input('difficulty_level', 'medium'),
            'points' => $request->input('points', 1),
            'time_limit' => $request->input('time_limit'),
        ]);

        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }

    private function normalizeCorrectAnswer($input) {
        if (is_array($input)) {
            return json_encode($input);
        }
        
        if (strpos($input, '][') !== false) {
            $input = trim($input, '[]');
            $groups = explode('][', $input);
            $array = array_map(function($group) {
                return explode(',', $group);
            }, $groups);
            return json_encode($array);
        }

        if (is_numeric($input) || in_array($input, ['true', 'false'])) {
            return json_encode([$input]);
        }
        
        throw new InvalidArgumentException('Invalid input format for correct_answer.');
    }
    
    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $exams = Exam::orderBy('title')->get();
        return view('admin.questions.edit', compact('question', 'exams'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank_text,fill_in_the_blank_choice,matching',
            'question_text' => 'required|string|max:5000',
            'description' => 'nullable|string|max:2000',
            'explanation' => 'nullable|string|max:2000',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:1000',
            'correct_answer' => 'required',
            'exam_id' => 'required|exists:exams,id',
            'is_active' => 'boolean',
            'difficulty_level' => 'nullable|string|in:easy,medium,hard',
            'points' => 'nullable|integer|min:1|max:100',
            'time_limit' => 'nullable|integer|min:1|max:3600',
        ]);

        $question = Question::findOrFail($id);

        if ($request->hasFile('image_name')) {
            if ($question->image_name) {
                Storage::disk('public')->delete($question->image_name);
            }
            $image = $request->file('image_name');
            $imagePath = $image->store('questions/images', 'public');
        } else {
            $imagePath = $question->image_name;
        }

        $correctAnswer = $this->normalizeCorrectAnswer($request->input('correct_answer'));

        $question->update([
            'question_type' => $request->input('question_type'),
            'question_text' => $request->input('question_text'),
            'description' => $request->input('description'),
            'explanation' => $request->input('explanation'),
            'image_name' => $imagePath,
            'options' => json_encode($request->input('options', [])),
            'correct_answer' => $correctAnswer,
            'exam_id' => $request->input('exam_id'),
            'is_active' => $request->boolean('is_active', true),
            'difficulty_level' => $request->input('difficulty_level', 'medium'),
            'points' => $request->input('points', 1),
            'time_limit' => $request->input('time_limit'),
        ]);

        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new QuestionsImport($request->exam_id), $request->file('file'));
            return redirect()->back()->with('success', 'Questions imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $question = Question::with('exam')->findOrFail($id);
        return view('admin.questions.show', compact('question'));
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        if ($question->image_name) {
            Storage::disk('public')->delete($question->image_name);
        }

        $question->delete();

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $question = Question::findOrFail($id);
        $question->is_active = !$question->is_active;
        $question->save();

        $status = $question->is_active ? 'activated' : 'deactivated';
        return response()->json([
            'success' => true,
            'message' => "Question {$status} successfully.",
            'status' => $question->is_active
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:questions,id'
        ]);

        $questions = Question::whereIn('id', $request->ids)->get();
        
        foreach ($questions as $question) {
            if ($question->image_name) {
                Storage::disk('public')->delete($question->image_name);
            }
        }

        Question::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' questions deleted successfully.'
        ]);
    }

    public function duplicate($id)
    {
        $question = Question::findOrFail($id);
        
        $newQuestion = $question->replicate();
        $newQuestion->question_text = $question->question_text . ' (Copy)';
        $newQuestion->is_active = false; // Make copies inactive by default
        $newQuestion->save();

        return redirect()->route('questions.index')->with('success', 'Question duplicated successfully.');
    }
}