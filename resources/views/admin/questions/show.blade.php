@extends('layouts.master')

@section('title')
View Question | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">View Question Details</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('questions.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                        <a class="btn btn-primary ms-2" href="{{ route('questions.edit', $question->id) }}" title="Edit">
                            <i class="material-icons">edit</i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <!-- Question Basic Info -->
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="material-icons me-2">info</i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Exam:</strong>
                                            <p class="text-muted">{{ $question->exam->title ?? 'No Exam Assigned' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Question Type:</strong>
                                            <span class="badge bg-info ms-2">
                                                {{ ucwords(str_replace('_', ' ', $question->question_type)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <strong>Status:</strong>
                                            <span class="badge {{ $question->is_active == 'active' ? 'bg-success' : ($question->is_active == 'draft' ? 'bg-warning' : 'bg-secondary') }} ms-2">
                                                {{ ucfirst($question->is_active) }}
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Created:</strong>
                                            <p class="text-muted">{{ $question->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question Content -->
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="material-icons me-2">quiz</i>Question Content</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Question Text:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {!! nl2br(e($question->question_text)) !!}
                                        </div>
                                    </div>
                                    
                                    @if($question->description)
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {!! nl2br(e($question->description)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    @if($question->explanation)
                                    <div class="mb-3">
                                        <strong>Explanation:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {!! nl2br(e($question->explanation)) !!}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Question Image -->
                        @if($question->image_name)
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="material-icons me-2">image</i>Question Image</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ asset('storage/' . $question->image_name) }}" 
                                         alt="Question Image" 
                                         class="img-fluid rounded shadow" 
                                         style="max-height: 400px;">
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $question->image_name) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="material-icons">open_in_new</i> View Full Size
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Question Options and Answers -->
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="material-icons me-2">list</i>Options & Correct Answers</h6>
                                </div>
                                <div class="card-body">
                                    @if($question->question_type == 'true_false')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="option-item p-3 rounded {{ json_decode($question->correct_answer)[0] === 'true' ? 'bg-success text-white' : 'bg-light' }}">
                                                    <i class="material-icons me-2">{{ json_decode($question->correct_answer)[0] === 'true' ? 'check_circle' : 'radio_button_unchecked' }}</i>
                                                    True
                                                    @if(json_decode($question->correct_answer)[0] === 'true')
                                                        <span class="badge bg-light text-success ms-2">Correct Answer</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="option-item p-3 rounded {{ json_decode($question->correct_answer)[0] === 'false' ? 'bg-success text-white' : 'bg-light' }}">
                                                    <i class="material-icons me-2">{{ json_decode($question->correct_answer)[0] === 'false' ? 'check_circle' : 'radio_button_unchecked' }}</i>
                                                    False
                                                    @if(json_decode($question->correct_answer)[0] === 'false')
                                                        <span class="badge bg-light text-success ms-2">Correct Answer</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    @elseif(in_array($question->question_type, ['single_choice', 'multiple_choice', 'fill_in_the_blank_with_choice']))
                                        @php
                                            $options = json_decode($question->options, true) ?? [];
                                            $correctAnswers = json_decode($question->correct_answer, true) ?? [];
                                            
                                            // Handle single choice correct answer format
                                            if ($question->question_type == 'single_choice' || $question->question_type == 'fill_in_the_blank_choice') {
                                                $correctAnswers = is_array($correctAnswers) ? $correctAnswers : [$correctAnswers];
                                            }
                                            
                                            // Convert string indices to integers for comparison
                                            $correctAnswers = array_map('intval', array_map('strval', $correctAnswers));
                                        @endphp
                                        
                                        @foreach($options as $index => $option)
                                            @if(!empty(trim($option)))
                                            <div class="option-item mb-2 p-3 rounded {{ in_array($index, $correctAnswers) ? 'bg-success text-white' : 'bg-light' }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-3">
                                                        @if($question->question_type == 'multiple_choice')
                                                            <i class="material-icons">{{ in_array($index, $correctAnswers) ? 'check_box' : 'check_box_outline_blank' }}</i>
                                                        @else
                                                            <i class="material-icons">{{ in_array($index, $correctAnswers) ? 'radio_button_checked' : 'radio_button_unchecked' }}</i>
                                                        @endif
                                                    </span>
                                                    <span class="me-2 fw-bold">{{ chr(65 + $index) }}.</span>
                                                    <span class="flex-grow-1">{{ $option }}</span>
                                                    @if(in_array($index, $correctAnswers))
                                                        <span class="badge bg-light text-success ms-2">Correct Answer</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach

                                    @elseif($question->question_type == 'fill_in_the_blank_with_text')
                                        @php
                                            $correctAnswers = json_decode($question->correct_answer, true) ?? [];
                                        @endphp
                                        
                                        <div class="alert alert-info">
                                            <i class="material-icons me-2">info</i>
                                            <strong>Fill in the blank question:</strong> Students need to type their answers in the blank spaces marked with [].
                                        </div>
                                        
                                        <div class="bg-light p-3 rounded">
                                            <strong>Correct Answers:</strong>
                                            <div class="mt-2">
                                                @if(is_array($correctAnswers) && count($correctAnswers) > 0)
                                                    @foreach($correctAnswers as $blankIndex => $blankAnswers)
                                                        <div class="mb-2">
                                                            <span class="badge bg-primary me-2">Blank {{ $blankIndex + 1 }}</span>
                                                            @if(is_array($blankAnswers))
                                                                {{ implode(', ', $blankAnswers) }}
                                                            @else
                                                                {{ $blankAnswers }}
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No correct answers defined</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-primary btn-lg px-5">
                                <i class="material-icons">edit</i> Edit Question
                            </a>
                            <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                                <i class="material-icons">list</i> Back to List
                            </a>
                            <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="d-inline-block ms-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg px-5" onclick="return confirm('Are you sure you want to delete this question?')">
                                    <i class="material-icons">delete</i> Delete Question
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.option-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.option-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.bg-success.option-item {
    border-color: #28a745;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.badge {
    font-size: 0.75em;
}

.material-icons {
    vertical-align: middle;
}
</style>
@endsection