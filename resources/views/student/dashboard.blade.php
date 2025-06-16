@extends('layouts.studentmaster')

@section('title')
    Student Portal | OEMS
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-light border-light shadow-sm">
                <h5 class="alert-heading mb-2">Welcome back, ! 👋</h5>
                <p class="mb-0">Here's your learning progress and upcoming activities.</p>
            </div>
        </div>
    </div>

    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <!-- Total Courses -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">menu_book</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Enrolled Courses</p>
                        <h4 class="mb-0">{{ $enrolledCoursesCount }}</h4>
                    </div>
                </div>
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <a href="{{ route('student.courses') }}" class="text-sm font-weight-bold">View All Courses</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Total Exams -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Exams</p>
                        <h4 class="mb-0">{{ $totalExamsCount }}</h4>
                    </div>
                </div>
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <a href="{{ route('student.exams') }}" class="text-sm font-weight-bold">View All Exams</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Completed Exams -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check_circle</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Completed Exams</p>
                        <h4 class="mb-0">{{ $completedExamsCount }}</h4>
                    </div>
                </div>
                <div class="card-footer p-3">
                    <p class="mb-0">
                        @if($completedExamsCount > 0)
                            <span class="text-success text-sm font-weight-bold">
                                {{ number_format(($completedExamsCount / $totalExamsCount) * 100, 1) }}% completion rate
                            </span>
                        @else
                            <span class="text-sm">No exams completed yet</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Pending Exams -->
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">hourglass_empty</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Pending Exams</p>
                        <h4 class="mb-0">{{ $pendingExamsCount }}</h4>
                    </div>
                </div>
                <div class="card-footer p-3">
                    <p class="mb-0">
                        @if($pendingExamsCount > 0)
                            <span class="text-info text-sm font-weight-bold">
                                <i class="fa fa-clock"></i> Action required
                            </span>
                        @else
                            <span class="text-sm">All caught up!</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row mb-4">
        <!-- Upcoming Exams -->
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Upcoming Exams</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-clock text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">Next {{ $upcomingExams->count() }} exams</span>
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <div class="dropdown float-lg-end pe-4">
                                <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v text-secondary"></i>
                                </a>
                                <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                    <li><a class="dropdown-item border-radius-md" href="{{ route('student.exams') }}">View All Exams</a></li>
                                    <li><a class="dropdown-item border-radius-md" href="{{ route('student.courses') }}">Manage Enrollments</a></li>
                                    <li><a class="dropdown-item border-radius-md" href="{{ route('student.exams') }}">Results Overview</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if($upcomingExams->isEmpty())
                        <div class="text-center py-4">
                            <i class="material-icons text-secondary" style="font-size: 48px;">assignment_turned_in</i>
                            <p class="text-secondary mt-2">No upcoming exams scheduled</p>
                            <a href="{{ route('student.exams') }}" class="btn btn-primary btn-sm">Browse Available Exams</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date & Time</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingExams as $exam)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $exam->exam->title }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $exam->exam->exam_code }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ \Carbon\Carbon::parse($exam->exam->start_time)->format('M d, Y') }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($exam->exam->start_time)->format('g:i A') }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-gradient-info">{{ ucfirst($exam->status) }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if(\Carbon\Carbon::parse($exam->exam->start_time)->isPast())
                                                    <span class="text-secondary text-xs">Exam started</span>
                                                @else
                                                    <a href="#" class="btn btn-primary btn-sm">
                                                        <i class="material-icons text-sm">play_arrow</i> Take Exam
                                                    </a>
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
        
        <!-- Activity Timeline -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Recent Activity</h6>
                    <p class="text-sm">
                        <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                        <span class="font-weight-bold">Your learning journey</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="timeline timeline-one-side">
                        @foreach($recentActivities as $activity)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="material-icons text-{{ $activity['color'] }} text-gradient">{{ $activity['icon'] }}</i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $activity['description'] }}</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">{{ $activity['date'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <!-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('student.courses') }}" class="btn btn-outline-primary w-100">
                                <i class="material-icons me-2">school</i>
                                View Courses
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('student.exams') }}" class="btn btn-outline-info w-100">
                                <i class="material-icons me-2">assignment</i>
                                Take Exam
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('student.profile') }}" class="btn btn-outline-success w-100">
                                <i class="material-icons me-2">person</i>
                                Update Profile
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="#" class="btn btn-outline-warning w-100">
                                <i class="material-icons me-2">help</i>
                                Get Help
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>
@endsection