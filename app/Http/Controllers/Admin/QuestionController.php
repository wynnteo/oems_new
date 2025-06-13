<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Exam;

class QuestionController extends Controller
{
    public function index(Request $request) 
    {
        $query = Question::with('exam');
        
        // Apply filters if provided
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        if ($request->filled('question_type')) {
            $query->where('question_type', $request->question_type);
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }
        
        $questions = $query->orderBy('created_at', 'desc')->paginate(25);
        $exams = Exam::select('id', 'title', 'exam_code')->get();
        
        // Get question types for filter dropdown
        $questionTypes = [
            'true_false' => 'True/False',
            'single_choice' => 'Single Choice',
            'multiple_choice' => 'Multiple Choice',
            'fill_in_the_blank_text' => 'Fill in the Blank (Text)',
            'fill_in_the_blank_choice' => 'Fill in the Blank (Choice)'
        ];

        $stats = [
            'total' => Question::count(),
            'active' => Question::where('is_active', true)->count(),
            'inactive' => Question::where('is_active', false)->count(),
            'by_type' => Question::selectRaw('question_type, COUNT(*) as count')
                              ->groupBy('question_type')
                              ->pluck('count', 'question_type')
                              ->toArray()
        ];
        
        return view('admin.questions.index', compact('questions', 'exams', 'questionTypes', 'stats'));
    }

    // Display the form to create a new question
    public function create($examId = null)
    {
        $exams = Exam::select('id', 'title', 'exam_code', 'status')
                    ->where('status', 'available')
                    ->orderBy('exam_code')
                    ->get();
        
        // Validate examId if provided
        if ($examId && !$exams->contains('id', $examId)) {
            return redirect()->route('questions.create')
                           ->with('error', 'Invalid exam selected.');
        }
        return view('admin.questions.create', compact('exams', 'examId'));
    }

    // Store a newly created question in storage
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank_text,fill_in_the_blank_choice',
            'question_text' => 'required|string|max:1000',
            'description' => 'nullable|string|max:500',
            'explanation' => 'nullable|string|max:1000',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'correct_answer' => 'required',
            'exam_id' => 'required|exists:exams,id',
            'is_active' => 'required|string|in:active,inactive',
        ]);

        // Check if exam exists and is available
        $exam = Exam::find($request->exam_id);
        if (!$exam) {
            throw ValidationException::withMessages([
                'exam_id' => 'Selected exam does not exist.'
            ]);
        }

        // Handle the image upload if present
        $imagePath = null;
        if ($request->hasFile('image_name')) {
            $imagePath = $request->file('image_name')->store('questions/images', 'public');
        }

        $correctAnswer = $this->normalizeCorrectAnswer($request->input('correct_answer'));

        // Create the question
        $question = Question::create([
            'question_type' => $validatedData['question_type'],
            'question_text' => $validatedData['question_text'],
            'description' => $validatedData['description'],
            'explanation' => $validatedData['explanation'] ?? null,
            'image_name' => $imagePath,
            'options' => json_encode($request->input('options', [])),
            'correct_answer' => $correctAnswer,
            'exam_id' => $validatedData['exam_id'],
            'is_active' => $validatedData['is_active'],
        ]);

        // Redirect with a success message
        return redirect()->route('questions.index')
                           ->with('success', 'Question created successfully.');
    }

    function normalizeCorrectAnswer($input) {
        // If input is already an array, encode it as JSON
        if (is_array($input)) {
            return json_encode($input);
        }
        
        if (strpos($input, '][') !== false) {
            // Remove outer brackets
            $input = trim($input, '[]');
            
            // Split by '][' to get groups
            $groups = explode('][', $input);
            
            // Convert each group into an array of its elements
            $array = array_map(function($group) {
                return explode(',', $group);
            }, $groups);
            
            // Return as JSON for storage
            return json_encode($array);
        }

        // Handle True/False or Single Choice format
        if (is_numeric($input) || in_array($input, ['true', 'false'])) {
            // Convert to a single array with one value
            return json_encode([$input]);
        }
        
        // Handle unexpected types
        throw new InvalidArgumentException('Invalid input format for correct_answer.');
    }
    
    public function edit($id)
    {
        try {
            $question = Question::with('exam')->findOrFail($id);
            $exams = Exam::select('id', 'title', 'exam_code')
                        ->orderBy('exam_code')
                        ->get();

            return view('admin.questions.edit', compact('question', 'exams'));
        } catch (\Exception $e) {
            return redirect()->route('questions.index')
                           ->with('error', 'Question not found.');
        }
    }

    // Update a specific question in storage
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank_text,fill_in_the_blank_choice',
            'question_text' => 'required|string|max:1000',
            'description' => 'nullable|string|max:500',
            'explanation' => 'nullable|string|max:1000',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'correct_answer' => 'required',
            'exam_id' => 'required|exists:exams,id',
            'is_active' => 'required|string|in:active,inactive',
        ]);

        // Find the question
        $question = Question::findOrFail($id);

        $imagePath = $question->image_name;
            
        if ($request->hasFile('image_name')) {
            // Delete the old image if it exists
            if ($question->image_name) {
                Storage::disk('public')->delete($question->image_name);
            }
            $imagePath = $request->file('image_name')->store('questions/images', 'public');
        }

        $correctAnswer = $this->normalizeCorrectAnswer($request->input('correct_answer'));

        // Update the question
        $question->update([
            'question_type' => $validatedData['question_type'],
            'question_text' => $validatedData['question_text'],
            'description' => $validatedData['description'],
            'explanation' => $validatedData['explanation'] ?? null,
            'image_name' => $imagePath,
            'options' => json_encode($request->input('options', [])),
            'correct_answer' => $correctAnswer,
            'exam_id' => $validatedData['exam_id'],
            'is_active' => $validatedData['is_active'],
        ]);

        return redirect()->route('questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|integer|exists:exams,id',
                'file' => 'required|file|mimes:xlsx,csv,xls|max:10240',
            ]);

            $exam = Exam::find($request->exam_id);
            if (!$exam) {
                return redirect()->back()
                               ->with('error', 'Selected exam does not exist.');
            }

            Excel::import(new QuestionsImport, $request->file('file'));

            return redirect()->back()
                           ->with('success', 'Questions imported successfully.');
                           
        } catch (ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $question = Question::with('exam')->findOrFail($id);
            return view('admin.questions.show', compact('question'));
        } catch (\Exception $e) {
            return redirect()->route('questions.index')
                           ->with('error', 'Question not found.');
        }
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        if ($question->image_name) {
            \Storage::disk('public')->delete($question->image_name);
        }

        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }

    public function toggleStatus(Request $request, Question $question)
    {
        try {
            $question->update(['is_active' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Question status updated successfully.'
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => 'Unable to update question status.'
            ], 500);
        }
    }
}