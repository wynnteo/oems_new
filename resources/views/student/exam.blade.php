@extends('layouts.exammaster')

@section('title')
    Exam | Student Portal
@endsection
<style>

</style>
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light text-white">
                    <h4 class="mb-0">{{ $exam->title }}</h4>
                </div>
                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="exam-description mb-4">
                        <p>{!! $exam->description !!}</p>
                    </div>
                    <div class="exam-instructions">
                        <h5>Please read the following instructions carefully before starting the exam:</h5>
                            <ul>
                                <li>Ensure you have a stable internet connection.</li>
                                <li>The exam has a time limit of <strong>{{ $exam->formatDuration() }}</strong>.</li>
                                <li>Once started, the exam cannot be paused.</li>
                                <li>All answers must be submitted before the time runs out.</li>
                                <li>Do not refresh the page during the exam.</li>
                                <li>Make sure you are in a quiet environment to avoid distractions.</li>
                            </ul>
                    </div>
                    <div class="text-center mt-4">
                        <form action="{{ route('student.exam.start', ['examId' => $exam->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                Start Exam
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    Good luck! Stay calm and do your best.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('contextmenu', function(e) {
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
    });
</script>
@endsection