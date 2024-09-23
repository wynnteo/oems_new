@extends('layouts.exammaster')

@section('title', 'Exam Summary')

@section('content')
<div class="container py-4">
    <h3>{{ $exam->title }} - {{ $exam->exam_code }}</h3>
    
    @if ($passFailStatus === 'Failed')
        <div class="alert text-center" role="alert">
            <i class="fa-solid fa-list-check" style="color: orange; font-size:60px;"></i>
            <div class="mb-4"></div>
            <h2 class="alert-heading">Keep Trying</h2>
            <div class="mb-4"></div>
            <p>Unfortunately, you failed the exam this fime.</p>
            <h5>Your scored {{ $correctAnswers }} / {{ $totalQuestions }}</h5>
            <h4>{{ $score }}%</h4>
        </div>
    @else
        <div class="alert alert-light text-center" role="alert">
            <i class="fa-solid fa-list-check" style="color: green; font-size:60px;"></i>
            <div class="mb-4"></div>
            <h2 class="alert-heading">You Passed</h2>
            <div class="mb-4"></div>
            <p>Congratulaitons on passing the exam!</p>
            <h5>Your scored {{ $correctAnswers }} / {{ $totalQuestions }}</h5>
            <h4>{{ $score }}%</h4>
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
                                @if($question['question_type'] != 'fill_in_the_blank_text')
                                    @if($question['question_type'] == 'fill_in_the_blank_choice')
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
                        <form>
                            <div class="options_panel">
                                @if($question['question_type'] == 'single_choice' || $question['question_type'] == 'fill_in_the_blank_choice')
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
                                        print_r($studentAnswer[0]);
                                    @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[]" id="true" value="true" {{ in_array('true', $studentAnswer) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="true">True</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[]" id="false" value="false" {{ in_array('false', $studentAnswer) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="false">False</label>
                                    </div>
                                
                                @elseif($question['question_type'] == 'fill_in_the_blank_text')
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
                        </form>
                    </div>
                @endforeach

            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Optional: JavaScript for any dynamic behavior
</script>
@endsection
