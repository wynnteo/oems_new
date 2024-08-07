<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Question; // Make sure to import your Question model

class QuestionController extends Controller
{
    // Display the form to create a new question
    public function create()
    {
        return view('admin.questions.create');
    }

    // Store a newly created question in storage
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'question_type' => 'required|string|in:true_false,single_choice,multiple_choice,fill_in_the_blank',
            'question_text' => 'required|string',
            'description' => 'nullable|string',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_answer' => 'required',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Handle the image upload if present
        $imagePath = null;
        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $imagePath = $image->store('images', 'public');
        }

        // Create the question
        $question = new Question();
        $question->question_type = $request->input('question_type');
        $question->question_text = $request->input('question_text');
        $question->description = $request->input('description');
        $question->image_name = $imagePath;
        $question->options = json_encode($request->input('options', []));
        $question->correct_answer = json_encode($request->input('correct_answer'));
        $question->course_id = $request->input('course_id');
        $question->save();

        // Redirect with a success message
        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }
}
