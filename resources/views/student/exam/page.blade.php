@extends('layouts.exammaster')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>{{ $exam->title }}</h1>
        </div>
        <div class="col-md-6 text-right">
            <h4>Question {{ $currentIndex + 1 }} of {{ $questions->count() }}</h4>
        </div>
    </div>
    <!-- Main Content -->
    <div class="row">
        <!-- Left Side: Question Panel -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5>{!! $currentQuestion->question_text !!}</h5>
                    <p>{{ $currentQuestion->description }}</p>

                    @if($currentQuestion->image_name)
                        <img src="{{ asset('storage/' . $currentQuestion->image_name) }}" alt="Question Image" class="img-fluid mb-3">
                    @endif

                    <form action="{{ route('student.exam.submit_answer', ['examId' => $exam->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                        <input type="hidden" name="session_key" value="{{ $session_key }}">
                        <input type="hidden" name="action" id="action">

                        <!-- Question Options -->
                        @if($currentQuestion->question_type == 'single_choice')
                            @foreach($currentQuestion->options as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}">
                                    <label class="form-check-label" for="option{{ $loop->index }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach

                        @elseif($currentQuestion->question_type == 'multiple_choice')
                            @foreach($currentQuestion->options as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answer[]" id="option{{ $loop->index }}" value="{{ $option }}">
                                    <label class="form-check-label" for="option{{ $loop->index }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach

                        @elseif($currentQuestion->question_type == 'true_false')
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer[]" id="true" value="true">
                                <label class="form-check-label" for="true">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer[]" id="false" value="false">
                                <label class="form-check-label" for="false">False</label>
                            </div>

                        @elseif($currentQuestion->question_type == 'fill_in_the_blank_text')
                            <p>
                                @foreach(explode('[]', $currentQuestion->question_text) as $index => $segment)
                                    {!! $segment !!}
                                    @if ($index < count(explode('[]', $currentQuestion->question_text)) - 1)
                                        <input type="text" name="answer[]" class="form-control d-inline-block w-auto">
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
                                                <option value="{{ $option }}">{{ $option }}</option>
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
                            <button type="submit" class="btn btn-secondary" name="action" value="previous" {{ $previousQuestion ? '' : 'disabled' }}>
                                Previous
                            </button>
                            <button type="submit" class="btn btn-primary" name="action" value="next" {{ $nextQuestion ? '' : 'disabled' }}>
                                Next
                            </button>
                            @if(!$nextQuestion)
                                <button type="submit" class="btn btn-success" name="action" value="submit">
                                    Submit Exam
                                </button>
                            @endif
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
                    <div class="d-grid gap-2">
                        @foreach($questions as $index => $question)
                            <a href="{{ route('exam.page', ['code' => $exam->id, 'session_key' => $session_key, 'question_index' => $index]) }}" 
                               class="btn btn-sm btn-outline-primary {{ $currentIndex == $index ? 'active' : '' }}">
                                Question {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timer Script -->
<script>
    // Add your JavaScript logic to initialize and manage the timer here
</script>
@endsection
