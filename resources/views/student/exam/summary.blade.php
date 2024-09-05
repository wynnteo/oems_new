@extends('layouts.exammaster')

@section('title', 'Exam Summary')

@section('content')
<div class="container py-4">
    <h3>{{ $exam->title }} - {{ $exam->exam_code }}</h3>
    
    @if ($examStatus === 'COMPLETED')
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Congratulations!</h4>
            <p>You have completed the exam.</p>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Exam Incomplete</h4>
            <p>Your exam has not been completed yet.</p>
        </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Your Results</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Total Questions</h5>
                    <p>{{ $totalQuestions }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Correct Answers</h5>
                    <p>{{ $correctAnswers }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Incorrect Answers</h5>
                    <p>{{ $incorrectAnswers }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Score</h5>
                    <p>{{ $score }}%</p>
                </div>
                <div class="col-md-6">
                    <h5>Status</h5>
                    <p>{{ $passFailStatus }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($showReview)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Review Your Answers</h5>
            </div>
            <div class="card-body">
                @foreach($questions as $question)
                    <div class="mb-3">
                        <h6>Question: {{ $question->text }}</h6>
                        <p>Your Answer: {{ $question->student_answer }}</p>
                        <p>Correct Answer: {{ $question->correct_answer }}</p>
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
