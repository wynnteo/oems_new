@extends('layouts.master')

@section('title')
Question | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Create New Question</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('questions.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body pb-2">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <!-- Question Text -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Question Text:</strong>
                                    <textarea name="question_text" class="form-control" placeholder="Enter the question text" required></textarea>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description:</strong>
                                    <textarea name="description" class="form-control" placeholder="Enter a description (optional)"></textarea>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Image (optional):</strong>
                                    <input type="file" name="image_name" class="form-control">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div id="admin-editor-lp_question" class="answer_panel">
                                    <div class="">
                                        <div class="actions">
                                            <strong>Question Answers</strong>
                                            <div class="toolbar_actions">
                                                <select id="question_type" name="question_type" class="form-control" required>
                                                    <option value="">Select Question Type</option>
                                                    <option value="true_false">True/False</option>
                                                    <option value="single_choice">Single Choice</option>
                                                    <option value="multiple_choice">Multiple Choice</option>
                                                    <option value="fill_in_the_blank">Fill in the Blank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="data-content">
                                        <div id="options-container" style="display: none;">
                                            <label>Number of Options: 
                                                <input type="number" id="num-options" min="1" value="1" required>
                                            </label>
                                        </div>
                                        <table id="options-table" style="display: none;" class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Option</th>
                                                    <th>Correct</th>
                                                </tr>
                                            </thead>
                                            <tbody id="options-body">
                                                <!-- Options will be dynamically inserted here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" class="btn bg-gradient-dark">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const questionTypeSelect = document.getElementById('question_type');
    const optionsContainer = document.getElementById('options-container');
    const numOptionsInput = document.getElementById('num-options');
    const optionsTable = document.getElementById('options-table');
    const optionsBody = document.getElementById('options-body');

    // Function to update options based on the selected type
    function updateOptions() {
        const questionType = questionTypeSelect.value;

        // Hide options-related elements by default
        optionsContainer.style.display = 'none';
        optionsTable.style.display = 'none';
        optionsBody.innerHTML = '';

        if (questionType === 'multiple_choice') {
            // Show number of options input and table for multiple choice
            optionsContainer.style.display = 'block';
            optionsTable.style.display = 'table';
            
            numOptionsInput.addEventListener('input', function () {
                const numOptions = this.value;
                optionsBody.innerHTML = '';

                for (let i = 0; i < numOptions; i++) {
                    optionsBody.innerHTML += `
                        <tr>
                            <td>${i + 1}</td>
                            <td><input type="text" name="options[${i}]" required></td>
                            <td><input type="checkbox" name="correct_answer[]" value="${i}"></td>
                        </tr>
                    `;
                }
            });
        } else if (questionType === 'true_false') {
            // Show table for true/false
            optionsContainer.style.display = 'none';
            optionsTable.style.display = 'table';
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>True</td>
                    <td><input type="radio" name="correct_answer" value="true" required></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>False</td>
                    <td><input type="radio" name="correct_answer" value="false" required></td>
                </tr>
            `;
        } else if (questionType === 'single_choice') {
            // Show table for single choice
            optionsContainer.style.display = 'none';
            optionsTable.style.display = 'table';
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td><input type="text" name="options[0]" required></td>
                    <td><input type="radio" name="correct_answer" value="0" required></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><input type="text" name="options[1]" required></td>
                    <td><input type="radio" name="correct_answer" value="1" required></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><input type="text" name="options[2]" required></td>
                    <td><input type="radio" name="correct_answer" value="2" required></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><input type="text" name="options[3]" required></td>
                    <td><input type="radio" name="correct_answer" value="3" required></td>
                </tr>
            `;
        } else if (questionType === 'fill_in_the_blank') {
            // Show input for fill in the blank
            optionsContainer.style.display = 'none';
            optionsTable.style.display = 'table';
            optionsBody.innerHTML = `
                <label>Correct Answer: <input type="text" name="correct_answer" required></label>
            `;
        }
    }

    // Initial update based on the current selection
    questionTypeSelect.addEventListener('change', updateOptions);
});
</script>
@endsection
