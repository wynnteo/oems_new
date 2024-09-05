@extends('layouts.studentmaster')

@section('title', 'Exams')

@section('content')
<div class="container-fluid py-4">
    <h5 class="mb-4">Exams</h5>

    <!-- Registered Exams Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Registered Exams</h5>
        </div>
        <div class="card-body">
            @if($registeredExams->isEmpty())
                <div class="alert alert-info" role="alert">
                    No registered exams found.
                </div>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width:150px">Exam Code</th>
                            <th>Title</th>
                            <th style="width:150px">Exam Date</th>
                            <th style="width:150px">Status</th>
                            <th style="width:150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registeredExams as $index => $exam)
                            <tr>
                                <td>{{ $exam->exam->exam_code }}</td>
                                <td>{{ $exam->exam->title }}</td>
                                <td>{{ $exam->exam->start_time }}</td>
                                <td>{{ $exam->status }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Upcoming Exams Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Upcoming Exams</h5>
        </div>
        <div class="card-body">
            @if($upcomingExams->isEmpty())
                <div class="alert alert-info" role="alert">
                    No upcoming exams found.
                </div>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width:150px">Exam Code</th>
                            <th>Title</th>
                            <th style="width:150px">Exam Date</th>
                            <th style="width:150px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingExams as $index => $exam)
                            <tr>
                                <td>{{ $exam->exam->exam_code }}</td>
                                <td>{{ $exam->exam->title }}</td>
                                <td>{{ $exam->exam->start_time }}</td>
                                <td>{{ $exam->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Completed Exams Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Completed Exams</h5>
        </div>
        <div class="card-body">
            @if($completedExams->isEmpty())
                <div class="alert alert-info" role="alert">
                    No completed exams found.
                </div>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width:150px">Exam Code</th>
                            <th>Title</th>
                            <th style="width:150px">Completion Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedExams as $index => $exam)
                            <tr>
                                <td>{{ $exam->exam->exam_code }}</td>
                                <td>{{ $exam->exam->title }}</td>
                                <td>{{ $exam->completed_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
