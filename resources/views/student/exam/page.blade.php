@extends('layouts.exammaster')

@section('content')
<style>
    .form-check-label {
        display: inline;
        padding-inline-start: 10px;
    }

    .options_panel {
        min-height: 300px;
    }

    #exam-timer {
        font-size: xx-large;
    }
</style>
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-10">
            <h3>{{ $exam->title }} - {{ $exam->exam_code }}</h3>
        </div>
        <div class="col-md-2 text-right">
            <h4>Question {{ $currentIndex + 1 }} of {{ $questions->count() }}</h4>
        </div>
    </div>
    <!-- Main Content -->
    <div class="row">
        <!-- Left Side: Question Panel -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="question_text">
                    @if($currentQuestion->question_type != 'fill_in_the_blank_text')
                        @if($currentQuestion->question_type == 'fill_in_the_blank_choice')
                            @php
                                $textWithBlanks = str_replace('[]', '_________', $currentQuestion->question_text);
                            @endphp
                            {!! $textWithBlanks !!}
                        @else
                            {!! $currentQuestion->question_text !!}
                        @endif
                    @endif
                    </div>
                    <p>{{ $currentQuestion->description }}</p>

                    @if($currentQuestion->image_name)
                        <img src="{{ asset('storage/' . $currentQuestion->image_name) }}" alt="Question Image" class="img-fluid mb-3">
                    @endif

                    <form action="{{ route('student.exam.submit_answer', ['examId' => $exam->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                        <input type="hidden" name="session_key" value="{{ $session_key }}">
                        <input type="hidden" name="action" id="action">
                        
                        <div class="options_panel">
                            @php
                                $progressItem = collect($progress)->firstWhere('question_id', $currentQuestion->id);
                                $studentAnswer = $progressItem['student_answer'] ?? [];
                            @endphp
                        
                            <!-- Question Options -->
                            @if($currentQuestion->question_type == 'single_choice' || $currentQuestion->question_type == 'fill_in_the_blank_choice')
                                @foreach($currentQuestion->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}"
                                            {{ in_array($option, $studentAnswer) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                        
                            @elseif($currentQuestion->question_type == 'multiple_choice')
                                @foreach($currentQuestion->options as $option)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}"
                                            {{ in_array($option, $studentAnswer) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                        
                            @elseif($currentQuestion->question_type == 'true_false')
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer[]" id="true" value="true" {{ in_array('true', $studentAnswer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="true">True</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer[]" id="false" value="false" {{ in_array('false', $studentAnswer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="false">False</label>
                                </div>
                        
                            @elseif($currentQuestion->question_type == 'fill_in_the_blank_text')
                                <p>
                                    @foreach(explode('[]', $currentQuestion->question_text) as $index => $segment)
                                        {!! $segment !!}
                                        @if ($index < count(explode('[]', $currentQuestion->question_text)) - 1)
                                            <input type="text" name="answer[]" class="form-control d-inline-block w-auto" value="{{ $studentAnswer[$index] ?? '' }}">
                                        @endif
                                    @endforeach
                                </p>
                            @endif
                        </div>
                    
                        <!-- Navigation Buttons -->
                        <div class="mt-4">
                            <input type="checkbox" name="question_marked_review" id="markForReview" {{ $progressItem['question_marked_review'] ?? false ? 'checked' : '' }}>
                            <label for="markForReview">Mark for Review</label>
                        </div>
                    
                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-secondary" name="action" value="previous" {{ $previousQuestion !== null ? '' : 'disabled' }}>
                                Previous
                            </button>
                            @if(!$nextQuestion)
                                <button type="submit" class="btn btn-success" name="action" value="submit">
                                    Submit Exam
                                </button>
                            @endif
                            <button type="submit" class="btn btn-primary" name="action" value="next" {{ $nextQuestion !== null ? '' : 'disabled' }}>
                                Next
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>

        <!-- Right Side: Navigation & Timer -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h4>Time Left</h4>
                    <div id="exam-timer">
                        <!-- Timer script will populate here -->
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5>Question Navigation</h5>
                    <div class="btn-grid">
                        @foreach($questions as $index => $question)
                            @php
                                // Find the corresponding progress entry for this question
                                $progressItem = $progress[$index];
                            @endphp
                            <a href="{{ route('exam.page', ['code' => $exam->id, 'session_key' => $session_key, 'question_index' => $index]) }}" 
                            class="btn btn-square {{ $currentIndex == $index ? 'active' : '' }} {{ $progressItem['question_marked_review'] ? 'reviewed' : '' }}">
                                {{ $index + 1 }}
                            </a>
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
    let timeLeftInSeconds = @json($timeLeftInSeconds);

    function updateTimer() {
        if (timeLeftInSeconds <= 0) {
            document.getElementById('exam-timer').innerText = "Time's up!";

            document.getElementById('action').value = 'submit';
            document.getElementById('examForm').submit();
            // Optionally, handle exam expiration here
            return;
        }

        let minutes = Math.floor(timeLeftInSeconds / 60);
        let seconds = Math.floor(timeLeftInSeconds % 60);
        
        // Format minutes and seconds with leading zeros
        let formattedMinutes = minutes.toString().padStart(2, '0');
        let formattedSeconds = seconds.toString().padStart(2, '0');

        // Update the timer display
        document.getElementById('exam-timer').innerText = `${formattedMinutes}:${formattedSeconds}`;
        
        timeLeftInSeconds--;
        setTimeout(updateTimer, 1000);
    }

    updateTimer();
    
    /*document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.shiftKey && e.key === 'J') || (e.ctrlKey && e.key === 'U')) {
            e.preventDefault();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) {
            e.preventDefault();
        }
    });*/
</script>
@endsection
