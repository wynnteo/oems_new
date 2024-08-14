<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Exam;

class QuestionController extends Controller
{
    public function index() 
    {
        $questions = Question::all();
        $exams = Exam::all();
        return view('admin.questions.index', compact('questions', 'exams'));
    }

    // Display the form to create a new question
    public function create($examId = null)
    {
        $exams = Exam::all();
        // Pass the exams and the optional examId to the view
        return view('admin.questions.create', compact('exams', 'examId'));
    }

    // Store a newly created question in storage
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank_text,fill_in_the_blank_choice',
            'question_text' => 'required|string',
            'description' => 'nullable|string',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'options' => 'nullable|array',
            'correct_answer' => 'required',
            'exam_id' => 'required|exists:exams,id',
        ]);

        // Handle the image upload if present
        $imagePath = null;
        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imagePath = $image->store('images', 'public');
        }

        $correctAnswer = $this->normalizeCorrectAnswer($request->input('correct_answer'));

        // Create the question
        $question = new Question();
        $question->question_type = $request->input('question_type');
        $question->question_text = $request->input('question_text');
        $question->description = $request->input('description');
        $question->image_name = $imagePath;
        $question->options = json_encode($request->input('options', []));
        $question->correct_answer = $correctAnswer;
        $question->exam_id = $request->input('exam_id');
        $question->save();

        // Redirect with a success message
        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }

    function normalizeCorrectAnswer($input) {
        // If input is already an array, encode it as JSON
        if (is_array($input)) {
            return json_encode($input);
        }
        
        // If input is a string in the format [Earth][planet,hello]
        if (is_string($input)) {
            // Remove the outermost square brackets
            $input = trim($input, '[]');
            
            // Split the string by '][' to separate the groups
            $groups = explode('][', $input);
            
            // Convert each group into an array of its elements
            $array = array_map(function($group) {
                return explode(',', $group);
            }, $groups);
            
            // Encode the result as JSON
            return json_encode($array);
        }
        
        // If input is a single value, wrap it in an array
        if (is_scalar($input)) {
            return json_encode([[$input]]);
        }
        
        // Handle unexpected types
        throw new InvalidArgumentException('Invalid input format for correct_answer.');
    }
    
    // Display the form to edit a specific question
    public function edit($id)
    {
        // Retrieve the question and exams
        $question = Question::findOrFail($id);
        $exams = Exam::all();

        // Pass the question and exams to the view
        return view('admin.questions.edit', compact('question', 'exams'));
    }

    // Update a specific question in storage
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank_text,fill_in_the_blank_choice',
            'question_text' => 'required|string',
            'description' => 'nullable|string',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'options' => 'nullable|array',
            'correct_answer' => 'required',
            'exam_id' => 'required|exists:exams,id',
        ]);

        // Find the question
        $question = Question::findOrFail($id);

        // Handle the image upload if present
        if ($request->hasFile('image_name')) {
            // Delete the old image if it exists
            if ($question->image_name) {
                \Storage::disk('public')->delete($question->image_name);
            }

            $image = $request->file('image_name');
            $imagePath = $image->store('images', 'public');
        } else {
            $imagePath = $question->image_name; // Keep the old image if no new one is uploaded
        }

        $correctAnswer = $this->normalizeCorrectAnswer($request->input('correct_answer'));

        // Update the question
        $question->question_type = $request->input('question_type');
        $question->question_text = $request->input('question_text');
        $question->description = $request->input('description');
        $question->image_name = $imagePath;
        $question->options = json_encode($request->input('options', []));
        $question->correct_answer = $correctAnswer;
        $question->exam_id = $request->input('exam_id');
        $question->save();

        // Redirect with a success message
        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }

    public function import(Request $request)
    {

    }

    // Display a specific question
    public function show($id)
    {
        // Retrieve the question
        $question = Question::findOrFail($id);

        // Pass the question to the view
        return view('admin.questions.show', compact('question'));
    }

    // Remove a specific question from storage
    public function destroy($id)
    {
        // Find the question
        $question = Question::findOrFail($id);

        // Delete the image if it exists
        if ($question->image_name) {
            \Storage::disk('public')->delete($question->image_name);
        }

        // Delete the question
        $question->delete();

        // Redirect with a success message
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }
}