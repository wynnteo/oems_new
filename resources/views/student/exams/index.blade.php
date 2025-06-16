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
                    <a href="{{route("student.exams.schedule")}}" class="btn btn-primary btn-sm">Schedule an Exam</a>
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
        <div class="card-header pb-0">
            <div class="row">
                <div class="col-lg-6 col-7">
                    <h6>Completed Exams</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                        <span class="font-weight-bold ms-1">{{ $completedExams->count() }} exams completed</span>
                    </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                    <div class="dropdown float-lg-end pe-4">
                        <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v text-secondary"></i>
                        </a>
                        <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                            <li><a class="dropdown-item border-radius-md" href="#">Export Results</a></li>
                            <li><a class="dropdown-item border-radius-md" href="#">Print Summary</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pb-2">
            @if($completedExams->isEmpty())
                <div class="text-center py-4">
                    <i class="material-icons text-secondary" style="font-size: 48px;">assignment_turned_in</i>
                    <h6 class="mt-3">No Completed Exams</h6>
                    <p class="text-secondary">Your completed exams and results will appear here.</p>
                </div>
            @else
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="completedExamsTable">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Completed</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Score</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Grade</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedExams as $exam)
                                @php
                                    $result = $exam->examResult;
                                    $passed = $result && $result->score >= $exam->exam->passing_grade;
                                    $status = $result ? ($passed ? 'Passed' : 'Failed') : 'Pending';
                                    $statusClass = $result ? ($passed ? 'bg-gradient-success' : 'bg-gradient-danger') : 'bg-gradient-secondary';
                                    $iconClass = $passed ? 'bg-gradient-success' : 'bg-gradient-danger';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <div class="avatar avatar-sm icon icon-shape {{ $iconClass }} shadow text-center border-radius-md">
                                                    <i class="material-icons opacity-10 text-sm">{{ $passed ? 'check_circle' : 'cancel' }}</i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center ms-3">
                                                <h6 class="mb-0 text-sm">{{ $exam->exam->title }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $exam->exam->exam_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ \Carbon\Carbon::parse($exam->completed_at)->format('M d, Y') }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($exam->completed_at)->format('g:i A') }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $result ? $result->score : ($exam->score ?? 'N/A') }}
                                            @if($result || $exam->score)
                                                / {{ $exam->exam->total_marks ?? 100 }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($result || $exam->score)
                                            @php
                                                $percentage = $result 
                                                    ? ($result->score / ($exam->exam->total_marks ?? 100)) * 100
                                                    : ($exam->score / ($exam->exam->total_marks ?? 100)) * 100;
                                            @endphp
                                            <span class="text-xs font-weight-bold">{{ number_format($percentage, 1) }}%</span>
                                        @else
                                            <span class="text-xs text-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm {{ $statusClass }}">{{ $status }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($result)
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="#" class="btn btn-info btn-sm me-1" title="View Result">
                                                    <i class="material-icons text-sm">visibility</i>
                                                </a>
                                                <a href="#" class="btn btn-outline-primary btn-sm" title="Download Certificate">
                                                    <i class="material-icons text-sm">download</i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-secondary text-xs">No Result</span>
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
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize DataTables for both tables
        $('#upcomingExamsTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            columnDefs: [
                {
                    targets: [2, 3, 4], // Duration, Status, Actions columns
                    orderable: false
                },
                {
                    targets: 4, // Actions column
                    width: '150px'
                }
            ],
            language: {
                search: "Search exams:",
                emptyTable: "No upcoming exams found",
                zeroRecords: "No matching exams found"
            }
        });

        $('#completedExamsTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            order: [[1, 'desc']], // Sort by completion date descending
            columnDefs: [
                {
                    targets: [2, 3, 4, 5], // Score, Grade, Status, Actions columns
                    orderable: false
                },
                {
                    targets: 5, // Actions column
                    width: '150px'
                }
            ],
            language: {
                search: "Search completed exams:",
                emptyTable: "No completed exams found",
                zeroRecords: "No matching exams found"
            }
        });
    });
</script>
@endsection