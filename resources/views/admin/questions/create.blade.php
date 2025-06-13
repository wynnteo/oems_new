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
                                   <select name="exam_id" class="form-control" required>
                                       <option value="">Select Exam</option>
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
                                    <textarea name="question_text" class="form-control" placeholder="Enter the question text" required>{{ old('question_text') }}</textarea>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description:</strong>
                                    <textarea name="description" class="form-control" placeholder="Enter a description (optional)">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Explanation -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Explanation (optional):</strong>
                                    <textarea name="explanation" class="form-control" placeholder="Enter explanation for the answer">{{ old('explanation') }}</textarea>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Image (optional):</strong>
                                    <input type="file" name="image_name" class="form-control" accept="image/*">
                                    @if (old('image_name'))
                                        <p>Previously uploaded file will not be shown here; re-upload if needed.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="is_active" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('is_active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="draft" {{ old('is_active') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
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
                                                <input type="number" id="num-options" min="2" max="10" value="4">
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

    // Safely parse old values from Blade to JavaScript
    const oldOptions = @json(old('options', []));
    let oldCorrectAnswers = @json(old('correct_answer', []));
    const oldQuestionType = @json(old('question_type', ''));

    // Safely handle oldCorrectAnswers
    if (typeof oldCorrectAnswers === 'string' && oldCorrectAnswers.trim() !== '') {
        try {
            oldCorrectAnswers = JSON.parse(oldCorrectAnswers);
        } catch (e) {
            oldCorrectAnswers = [oldCorrectAnswers];
        }
    }
    if (!Array.isArray(oldCorrectAnswers)) {
        oldCorrectAnswers = oldCorrectAnswers ? [oldCorrectAnswers] : [];
    }

    function updateOptions() {
        const questionType = questionTypeSelect.value;

        // Hide all containers by default
        optionsContainer.style.display = 'none';
        optionsTable.style.display = 'none';
        instructionText.style.display = 'none';

        // Clear previous content
        optionsBody.innerHTML = '';

        // Set up options based on the selected question type
        if (questionType === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            optionsTable.style.display = 'table';
            
            const currentNumOptions = oldOptions.length > 0 ? oldOptions.length : 4;
            numOptionsInput.value = currentNumOptions;
            
            generateMultipleChoiceOptions(currentNumOptions);

            numOptionsInput.addEventListener('input', function () {
                const newNumOptions = Math.max(2, Math.min(10, parseInt(this.value) || 4));
                this.value = newNumOptions;
                generateMultipleChoiceOptions(newNumOptions);
            });

        } else if (questionType === 'true_false') {
            optionsTable.style.display = 'table';
            const correctAnswer = oldCorrectAnswers.length > 0 ? oldCorrectAnswers[0] : '';
            
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>True</td>
                    <td><input type="radio" name="correct_answer" value="true" ${correctAnswer === 'true' ? 'checked' : ''} required></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>False</td>
                    <td><input type="radio" name="correct_answer" value="false" ${correctAnswer === 'false' ? 'checked' : ''} required></td>
                </tr>
            `;

        } else if (questionType === 'single_choice' || questionType === 'fill_in_the_blank_choice') {
            if (questionType === 'fill_in_the_blank_choice') {
                instructionText.style.display = 'block';
            }
            
            optionsTable.style.display = 'table';
            const correctAnswer = oldCorrectAnswers.length > 0 ? oldCorrectAnswers[0] : '';
            
            for (let i = 0; i < 4; i++) {
                const optionValue = oldOptions[i] || '';
                const checked = correctAnswer == i.toString() ? 'checked' : '';
                
                optionsBody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td><input type="text" name="options[${i}]" value="${escapeHtml(optionValue)}" placeholder="Option ${i + 1}"></td>
                        <td><input type="radio" name="correct_answer" value="${i}" ${checked} required></td>
                    </tr>
                `;
            }

        } else if (questionType === 'fill_in_the_blank_text') {
            instructionText.style.display = 'block';
            optionsTable.style.display = 'table';
            
            const correctAnswerText = formatCorrectAnswerForDisplay(oldCorrectAnswers);
            
            optionsBody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>
                        <input type="text" name="correct_answer" value="${escapeHtml(correctAnswerText)}" placeholder="Enter correct answers" required>
                        <br/>
                        <small><em>For multiple blanks, use format: [answer1,alternative1][answer2,alternative2]. Example: [is][meet,see]</em></small> 
                    </td>
                    <td></td>
                </tr>
            `;
        }
    }

    function generateMultipleChoiceOptions(numOptions) {
        optionsBody.innerHTML = '';
        const correctAnswersSet = new Set(oldCorrectAnswers.map(String));
        
        for (let i = 0; i < numOptions; i++) {
            const optionValue = oldOptions[i] || '';
            const checked = correctAnswersSet.has(i.toString()) ? 'checked' : '';
            
            optionsBody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td><input type="text" name="options[${i}]" value="${escapeHtml(optionValue)}" placeholder="Option ${i + 1}"></td>
                    <td><input type="checkbox" name="correct_answer[]" value="${i}" ${checked}></td>
                </tr>
            `;
        }
    }

    function formatCorrectAnswerForDisplay(answers) {
        if (!answers || answers.length === 0) return '';
        
        // If it's already formatted as [answer1][answer2], return as is
        if (typeof answers === 'string') return answers;
        
        // If it's an array of arrays, format it
        if (Array.isArray(answers)) {
            return answers.map(group => {
                if (Array.isArray(group)) {
                    return `[${group.join(',')}]`;
                }
                return `[${group}]`;
            }).join('');
        }
        
        return answers.toString();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Event listener for question type change
    questionTypeSelect.addEventListener('change', updateOptions);

    // Initialize options with existing values if available
    if (oldQuestionType) {
        questionTypeSelect.value = oldQuestionType;
    }
    updateOptions();
});
</script>
@endsection