@extends('layouts.exammaster')

@section('title', 'Exam Summary')

@section('content')
<div class="container py-4">
    <h3>{{ $exam->title }} - {{ $exam->exam_code }}</h3>
    
     @if ($passFailStatus === 'Failed')
        <div class="alert alert-light text-center" role="alert">
            <i class="fa-solid fa-exclamation-triangle" style="color: orange; font-size:60px;"></i>
            <div class="mb-4"></div>
            <h2 class="alert-heading">Keep Trying</h2>
            <div class="mb-4"></div>
            <p>Unfortunately, you failed the exam this time.</p>
            <h5>You scored {{ $correctAnswers }} / {{ $totalQuestions }}</h5>
            <h4>{{ number_format($score, 1) }}%</h4>
        </div>
    @else
        <div class="alert alert-light text-center" role="alert">
            <i class="fa-solid fa-trophy" style="color: green; font-size:60px;"></i>
            <div class="mb-4"></div>
            <h2 class="alert-heading">Congratulations! You Passed</h2>
            <div class="mb-4"></div>
            <p>Congratulations on passing the exam!</p>
            <h5>You scored {{ $correctAnswers }} / {{ $totalQuestions }}</h5>
            <h4>{{ number_format($score, 1) }}%</h4>
        </div>
    @endif

    @if ($showReview)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Review Your Answers</h5>
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
                            <p>{{ $question['description'] }}</p>

                            @if($question['image_name'])
                                <img src="{{ asset('storage/' . $question['image_name']) }}" alt="Question Image" class="img-fluid mb-3">
                            @endif
                        </div>
                   
                        <div class="options_panel">
                            @if($question['question_type'] == 'single_choice' || $question['question_type'] == 'fill_in_the_blank_with_choice')
                                @foreach($question['options'] as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}"
                                            {{ in_array($option, $question['student_answer']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            
                            @elseif($question['question_type'] == 'multiple_choice')
                                @foreach($question['options'] as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}"
                                            {{ in_array($option, $question['student_answer']) ? 'checked' : '' }}>
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
    @endif

    <!-- Feedback Section -->
    <div class="feedback-section">
        <h5><i class="fas fa-star me-2"></i>Rate This Exam</h5>
        <p class="text-muted">Help us improve by sharing your experience with this exam.</p>
        
        <form action="{{ route('exam.feedback', ['code' => $exam->id, 'session_key' => request()->route('session_key')]) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Rating</label>
                <div class="rating-container">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="rating-stars" data-rating="{{ $i }}">★</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" required>
                <small class="form-text text-muted">Click on stars to rate (1 = Poor, 5 = Excellent)</small>
            </div>
            
            <div class="mb-3">
                <label for="feedback" class="form-label">Feedback (Optional)</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" 
                          placeholder="Share your thoughts about this exam..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Submit Feedback
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Optional: JavaScript for any dynamic behavior
</script>
@endsection
