@extends('layouts.master')

@section('title')
Exam Result | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Exam Result</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('exams.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span>{{ $message }}</span>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="material-icons me-2">assignment</i> Exam Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Exam Code:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->exam->exam_code }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Title:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->exam->title }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Start At:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->started_at->format('Y-m-d H:i') }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Completed At:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->completed_at->format('Y-m-d H:i') }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Attempt Number:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Grade:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            @if ($result->score > $result->studentExam->exam->passing_grade)
                                                <span class="badge bg-success">Pass</span>
                                            @else
                                                <span class="badge bg-danger">Fail</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Score:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->score }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="material-icons me-2">person</i> Student Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Student Name:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->student->name }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Student Number:</strong>
                                        </div> 
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->student->student_code }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Gender:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->student->gender }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4">
                                            <strong>Email:</strong>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            {{ $result->studentExam->student->email }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Review</h5>
                        </div>
                        <div class="card-body">
                            @foreach($review as $question)
                                <div class="review_box">
                                    <div class="review_qns">
                                        <h6>
                                            
                                            @if($question['result'] === 'correct')
                                                <i class="fas fa-check-circle" style="color: green;"></i>
                                            @else
                                                <i class="fas fa-times-circle" style="color: red;"></i>
                                            @endif
            
                                            Question {{ $loop->iteration }}
                                        </h6>
                                        <div class="question_text">
                                            @if($question['question_type'] != 'fill_in_the_blank_with_text')
                                                @if($question['question_type'] == 'fill_in_the_blank_with_choice')
                                                    @php
                                                        $textWithBlanks = str_replace('[]', '_________', $question['question_text']);
                                                    @endphp
                                                    {!! $textWithBlanks !!}
                                                @else
                                                    {!! $question['question_text'] !!}
                                                @endif
                                            @endif
                                        </div>
                                        @if($question['image_name'])
                                            <img src="{{ asset('storage/' . $question['image_name']) }}" alt="Question Image" class="img-fluid mb-3">
                                        @endif
                                    </div>
                                    <div class="options_panel">
                                        @if($question['question_type'] == 'single_choice' || $question['question_type'] == 'fill_in_the_blank_with_choice')
                                            @foreach($question['options'] as $option)
                                            @php
                                                    $isStudentAnswer = is_array($question['student_answer']) && in_array($option, $question['student_answer']);
                                                @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="answer_{{ $question['question_id'] }}" id="option{{ $loop->parent->iteration }}_{{ $loop->index }}" value="{{ $option }}"
                                                        {{ $isStudentAnswer ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="option{{ $loop->parent->iteration }}_{{ $loop->index }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        
                                        @elseif($question['question_type'] == 'multiple_choice')
                                            @foreach($question['options'] as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}"
                                                        {{ in_array($option, $question['student_answer']) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="option{{ $loop->index }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        
                                        @elseif($question['question_type'] == 'true_false')
                                            @php
                                                $studentAnswer = $question['student_answer'];
                                            @endphp
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="answer[]" id="true" value="true" {{ in_array('true', $studentAnswer) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="true">True</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="answer[]" id="false" value="false" {{ in_array('false', $studentAnswer) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="false">False</label>
                                            </div>
                                        
                                        @elseif($question['question_type'] == 'fill_in_the_blank_with_text')
                                            <p>
                                                @foreach(explode('[]', $question['question_text']) as $index => $segment)
                                                    {!! $segment !!}
                                                    @if ($index < count(explode('[]', $question['question_text'])) - 1)
                                                        <input type="text" name="answer[]" class="form-control d-inline-block w-auto" value="{{ $question['student_answer'][$index] ?? '' }}">
                                                    @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    </div>
                                   <!-- Correct Answer -->
                                @if($question['result'] !== 'correct')
                                    <div class="correct-answer">
                                        <strong><i class="fas fa-check-circle text-success me-1"></i>Correct Answer:</strong>
                                        @if(is_array($question['correct_answer']))
                                            @if($question['question_type'] == 'fill_in_the_blank_with_text')
                                                <!-- For fill in the blank, show correct answers in context -->
                                                <div class="mt-2">
                                                    @foreach(explode('[]', $question['question_text']) as $index => $segment)
                                                        {!! $segment !!}
                                                        @if ($index < count(explode('[]', $question['question_text'])) - 1)
                                                            @php
                                                                $correctAnswers = is_array($question['correct_answer'][$index]) 
                                                                    ? $question['correct_answer'][$index] 
                                                                    : [$question['correct_answer'][$index]];
                                                            @endphp
                                                            <strong class="text-success">[{{ implode(' / ', $correctAnswers) }}]</strong>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif(in_array($question['question_type'], ['single_choice', 'multiple_choice', 'fill_in_the_blank_with_choice']))
                                                <!-- For multiple choice, show option letters and text -->
                                                <ul class="mb-0 mt-2">
                                                    @foreach($question['correct_answer'] as $correctIndex)
                                                        @if(isset($question['options'][$correctIndex]))
                                                            <li>{{ chr(65 + $correctIndex) }}. {{ $question['options'][$correctIndex] }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="ms-2">{{ implode(', ', $question['correct_answer']) }}</span>
                                            @endif
                                        @else
                                            <span class="ms-2">{{ $question['correct_answer'] }}</span>
                                        @endif
                                    </div>
                                @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection


    @section('scripts')
    <script>
        
    </script>
    @endsection