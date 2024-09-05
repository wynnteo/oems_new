@extends('layouts.exammaster')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-10">
            <h3>{{ $exam->title }}</h3>
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
                    <div class="question_text">{!! $currentQuestion->question_text !!}</div>
                    <p>{{ $currentQuestion->description }}</p>

                    @if($currentQuestion->image_name)
                        <img src="{{ asset('storage/' . $currentQuestion->image_name) }}" alt="Question Image" class="img-fluid mb-3">
                    @endif

                    <form action="{{ route('student.exam.submit_answer', ['examId' => $exam->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                        <input type="hidden" name="session_key" value="{{ $session_key }}">
                        <input type="hidden" name="action" id="action">
                    
                        @php
                            // Get the progress item for the current question
                            $progressItem = collect($progress)->firstWhere('question_id', $currentQuestion->id);
                            $studentAnswer = $progressItem['student_answer'] ?? [];
                        @endphp
                    
                        <!-- Question Options -->
                        @if($currentQuestion->question_type == 'single_choice')
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
                    
                        @elseif($currentQuestion->question_type == 'fill_in_the_blank_choice')
                            <p>
                                @foreach(explode('[]', $currentQuestion->question_text) as $index => $segment)
                                    {!! $segment !!}
                                    @if ($index < count(explode('[]', $currentQuestion->question_text)) - 1)
                                        <select name="answer[]" class="form-control d-inline-block w-auto">
                                            @foreach($currentQuestion->options as $option)
                                                <option value="{{ $option }}" {{ ($studentAnswer[$index] ?? '') == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                @endforeach
                            </p>
                        @endif
                    
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
