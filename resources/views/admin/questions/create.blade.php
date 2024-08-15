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

                            <!-- Hidden field to store exam ID if present -->
                            @if(old('exam_id') || $examId)
                                <input type="hidden" name="exam_id" value="{{ old('exam_id', $examId) }}">
                            @endif

                            <!-- Exam -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                               <div class="form-group">
                                   <strong>Exam:</strong>
                                   <select name="exam_id" class="form-control">
                                       @foreach ($exams as $exam)
                                           <option value="{{ $exam->id }}" {{ old('exam_id', $examId) == $exam->id ? 'selected' : '' }}>
                                               {{ $exam->title }}
                                           </option>
                                       @endforeach
                                   </select>
                               </div>
                           </div>

                            <!-- Question Type -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Question Type:</strong>
                                    <select id="question_type" name="question_type" class="form-control" required>
                                        <option value="">Select Question Type</option>
                                        <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                                        <option value="single_choice" {{ old('question_type') == 'single_choice' ? 'selected' : '' }}>Single Choice</option>
                                        <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                        <option value="fill_in_the_blank_choice" {{ old('question_type') == 'fill_in_the_blank_choice' ? 'selected' : '' }}>Fill in the Blank with Choice</option>
                                        <option value="fill_in_the_blank_text" {{ old('question_type') == 'fill_in_the_blank_text' ? 'selected' : '' }}>Fill in the Blank with Text</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Question Text:</strong>
                                    <small id="instruction-text" style="display: none;"><em>Using [] for blanks. Example: My name [] John. Nice to [] you?</em></small>              
                                    <textarea name="question_text" class="form-control" placeholder="Enter the question text" >{{ old('question_text') }}</textarea>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description:</strong>
                                    <textarea name="description" class="form-control" placeholder="Enter a description (optional)">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Image (optional):</strong>
                                    <input type="file" name="image_name" class="form-control">
                                    @if (old('image_name'))
                                        <p>Previously uploaded file will not be shown here; re-upload if needed.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 mb-5">
                                <div id="admin-editor-lp_question" class="answer_panel">
                                    <div class="">
                                        <div class="actions">
                                            <strong>Question Answers</strong>
                                        </div>
                                    </div>
                                    <div class="data-content">
                                        <div id="options-container" style="display: none;">
                                            <label>Number of Options:
                                                <input type="number" id="num-options" min="1" value="{{ old('options') ? count(old('options')) : 1 }}" >
                                            </label>
                                        </div>
                                        <table id="options-table" style="display: none;" class="table">
                                            <thead>
                                                <tr>
                                                    <th class="order_cl">#</th>
                                                    <th class="qns_cl">Options <small><em>Leave blank if you want to delete the option</em></small></th>
                                                    <th class="ans_cl">Correct?</th>
                                                </tr>
                                            </thead>
                                            <tbody id="options-body">
                                                
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
    const instructionText = document.getElementById('instruction-text');

    // Pass old values from Blade to JavaScript
    const oldOptions = @json(old('options', []));
    const oldCorrectAnswers = @json(old('correct_answer', []));
    const oldQuestionType = @json(old('question_type', ''));

    function updateOptions() {
        const questionType = questionTypeSelect.value;

        // Hide options container by default
        optionsContainer.style.display = 'none';
        optionsTable.style.display = 'none';

        // Set up options based on the selected question type
        if (questionType === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            optionsTable.style.display = 'table';
            
            const numOptions = numOptionsInput.value || oldOptions.length; // Default to existing count

            optionsBody.innerHTML = '';
            const correctAnswersSet = new Set(oldCorrectAnswers.map(Number));
            for (let i = 0; i < numOptions; i++) {
                const optionValue = oldOptions[i] || '';
                const checked = correctAnswersSet.has(i) ? 'checked' : '';
                optionsBody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td><input type="text" name="options[${i}]" value="${optionValue}" ></td>
                        <td><input type="checkbox" name="correct_answer[]" value="${i}" ${checked}></td>
                    </tr>
                `;
            }

            numOptionsInput.addEventListener('input', function () {
                const newNumOptions = this.value || oldOptions.length; // Default to existing count
                optionsBody.innerHTML = '';

                for (let i = 0; i < newNumOptions; i++) {
                    const optionValue = oldOptions[i] || '';
                    const checked = correctAnswersSet.has(i) ? 'checked' : '';
            
                    optionsBody.innerHTML += `
                        <tr>
                            <td>${i + 1}</td>
                            <td><input type="text" name="options[${i}]" value="${optionValue}" ></td>
                            <td><input type="checkbox" name="correct_answer[]" value="${i}" ${checked}></td>
                        </tr>
                    `;
                }
            });
        } else if (questionType === 'true_false') {
            optionsContainer.style.display = 'none';
            optionsTable.style.display = 'table';
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>True</td>
                    <td><input type="radio" name="correct_answer" value="true" ${oldCorrectAnswers == "true" ? 'checked' : ''}></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>False</td>
                    <td><input type="radio" name="correct_answer" value="false" ${oldCorrectAnswers == "false" ? 'checked' : ''}></td>
                </tr>
            `;
        } else if (questionType === 'single_choice' || questionType === 'fill_in_the_blank_choice') {
            optionsContainer.style.display = 'none';
            optionsTable.style.display = 'table';
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td><input type="text" name="options[0]" value="${oldOptions[0] || ''}" ></td>
                    <td><input type="radio" name="correct_answer" value="0" ${oldCorrectAnswers == "0" ? 'checked' : ''} ></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><input type="text" name="options[1]" value="${oldOptions[1] || ''}" ></td>
                    <td><input type="radio" name="correct_answer" value="1" ${oldCorrectAnswers == "1" ? 'checked' : ''} ></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><input type="text" name="options[2]" value="${oldOptions[2] || ''}" ></td>
                    <td><input type="radio" name="correct_answer" value="2" ${oldCorrectAnswers == "2" ? 'checked' : ''} ></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><input type="text" name="options[3]" value="${oldOptions[3] || ''}" ></td>
                    <td><input type="radio" name="correct_answer" value="3" ${oldCorrectAnswers == "3" ? 'checked' : ''} ></td>
                </tr>
            `;
        } else if (questionType === 'fill_in_the_blank_text') {
            optionsContainer.style.display = 'none';
            optionsTable.style.display = 'table';
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td><input type="text" name="correct_answer" value="${formatCorrectAnswer(oldCorrectAnswers) || ''}" >
                        <br/>
                        <small><em>If several possible answers for some blanks, seperate answers with semicolon. Example; [is][meet,see]</em></small> 
                    </td>
                    <td></td>
                </tr>
            `;
        }

        updateInstructionText()
    }

    function formatCorrectAnswer(array) {
        return array.map(group => `[${group.join(',')}]`).join('');
    }

    function updateInstructionText() {
        const questionType = questionTypeSelect.value;
        // Show instruction text only for 'fill_in_the_blank_choice' and 'fill_in_the_blank_text'
        if (questionType === 'fill_in_the_blank_choice' || questionType === 'fill_in_the_blank_text') {
            instructionText.style.display = 'block';
        } else {
            instructionText.style.display = 'none';
        }
    }
    

    questionTypeSelect.addEventListener('change', updateOptions);

    // Initialize options with existing values if available
    updateOptions();
});
</script>
@endsection
