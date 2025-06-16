@extends('layouts.studentmaster')

@section('title', 'Exams')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h5 class="mb-0">My Exams</h5>
            <p class="text-sm mb-0">Track your exam schedules and results</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="#" class="btn btn-primary">
                <i class="material-icons me-2">add</i>
                Schedule New Exam
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Exams</p>
                                <h5 class="font-weight-bolder mb-0">{{ $registeredExams->count() + $completedExams->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">assignment</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Upcoming</p>
                                <h5 class="font-weight-bolder mb-0">{{ $registeredExams->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">schedule</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Completed</p>
                                <h5 class="font-weight-bolder mb-0">{{ $completedExams->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">check_circle</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pass Rate</p>
                                <h5 class="font-weight-bolder mb-0">
                                    @php
                                        $totalCompleted = $completedExams->count();
                                        $passed = $completedExams->filter(function($exam) {
                                            $result = $exam->examResult;
                                            return $result && $result->score >= $exam->exam->passing_grade;
                                        })->count();
                                        echo $totalCompleted > 0 ? number_format(($passed / $totalCompleted) * 100, 1) . '%' : '0%';
                                    @endphp
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">trending_up</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams Section -->
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="row">
                <div class="col-lg-6 col-7">
                    <h6>Upcoming Exams</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-clock text-info" aria-hidden="true"></i>
                        <span class="font-weight-bold ms-1">{{ $registeredExams->count() }} exams scheduled</span>
                    </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                    <a class="btn btn-outline-primary btn-sm mb-0" href="#">
                        <i class="material-icons me-1">add</i> Schedule Exam
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pb-2">
            @if($registeredExams->isEmpty())
                <div class="text-center py-4">
                    <i class="material-icons text-secondary" style="font-size: 48px;">event_available</i>
                    <h6 class="mt-3">No Upcoming Exams</h6>
                    <p class="text-secondary">Schedule your next exam to continue your learning journey.</p>
                    <a href="#" class="btn btn-primary btn-sm">Schedule an Exam</a>
                </div>
            @else
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="upcomingExamsTable">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date & Time</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Duration</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registeredExams as $exam)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <div class="avatar avatar-sm icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                                    <i class="material-icons opacity-10 text-sm">assignment</i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center ms-3">
                                                <h6 class="mb-0 text-sm">{{ $exam->exam->title }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $exam->exam->exam_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ \Carbon\Carbon::parse($exam->exam->start_time)->format('M d, Y') }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($exam->exam->start_time)->format('g:i A') }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $exam->exam->duration ?? 120 }} mins</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @php
                                            $examTime = \Carbon\Carbon::parse($exam->exam->start_time);
                                            $now = now();
                                            $canTake = $examTime->subMinutes(15)->isPast() && $examTime->addMinutes(15)->isFuture();
                                        @endphp
                                        @if($canTake)
                                            <span class="badge badge-sm bg-gradient-success">Ready to Start</span>
                                        @elseif($examTime->isFuture())
                                            <span class="badge badge-sm bg-gradient-info">Scheduled</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Missed</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($canTake)
                                            <a href="#" class="btn btn-success btn-sm">
                                                <i class="material-icons text-sm">play_arrow</i> Start Exam
                                            </a>
                                        @elseif($examTime->isFuture())
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="#" class="btn btn-info btn-sm me-1" title="Exam Details">
                                                    <i class="material-icons text-sm">info</i>
                                                </a>
                                                <a href="#" class="btn btn-warning btn-sm" title="Reschedule">
                                                    <i class="material-icons text-sm">schedule</i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-secondary text-xs">Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                <table class="table table-striped" id="cexamtable">
                    <thead>
                        <tr>
                            <th style="width:150px">Exam Code</th>
                            <th>Title</th>
                            <th style="width:150px">Start At</th>
                            <th style="width:150px">Completed At</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedExams as $index => $exam)

                        @php
                            $result = $exam->examResult;
                            $status = $result && $result->score > $exam->exam->passing_grade ? 'Pass' : ($result ? 'Fail' : 'Not Graded');
                            $statusClass = $result
                                ? ($result->score > $exam->exam->passing_grade ? 'bg-success' : 'bg-danger')
                                : 'bg-secondary';
                        @endphp
                            <tr>
                                <td>{{ $exam->exam->exam_code }}</td>
                                <td>{{ $exam->exam->title }}</td>
                                <td>{{ $exam->started_at }}</td>
                                <td>{{ $exam->completed_at }}</td>
                                <td>{{ $exam->score }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $status }}</span></td>
                                <td>
                                    @if($result)
                                        <a href="" class="btn btn-primary btn-sm">
                                            <i class="material-icons">remove_red_eye</i> View
                                        </a>
                                    @else
                                        <span class="text-muted">No Result</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {
            $('#examtable').DataTable({
                columnDefs: [
                {
                    targets: 0,
                    width: '100px' 
                },
                {
                    targets: -1,
                    width: '100px'
                }]
            });

            $('#cexamtable').DataTable({
                columnDefs: [
                {
                    targets: 0,
                    width: '100px' 
                },
                {
                    targets: -1,
                    width: '100px'
                }]
            });
            
        });
    </script>
    @endsection