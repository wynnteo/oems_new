@extends('layouts.studentmaster')

@section('title', 'Enrolled Courses')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h5 class="mb-0">My Enrolled Courses</h5>
            <p class="text-sm mb-0">Manage your enrolled courses and track your progress</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="#" class="btn btn-primary">
                <i class="material-icons me-2">add</i>
                Browse More Courses
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Courses</p>
                                <h5 class="font-weight-bolder mb-0">{{ $enrollments->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">school</i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Courses</p>
                                <h5 class="font-weight-bolder mb-0">{{ $enrollments->where('status', 'active')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">trending_up</i>
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
                                <h5 class="font-weight-bolder mb-0">{{ $enrollments->where('status', 'completed')->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">This Month</p>
                                <h5 class="font-weight-bolder mb-0">{{ $enrollments->where('enrollment_date', '>=', now()->startOfMonth())->count() }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="material-icons opacity-10">calendar_today</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses List -->
    @if($enrollments->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="material-icons text-secondary" style="font-size: 72px;">school</i>
                        <h5 class="mt-3">No Courses Enrolled Yet</h5>
                        <p class="text-secondary">Start your learning journey by enrolling in courses that interest you.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="material-icons me-2">explore</i>
                            Browse Available Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-lg-6 col-7">
                                <h6>Your Enrolled Courses</h6>
                                <p class="text-sm mb-0">
                                    <i class="fa fa-check text-info" aria-hidden="true"></i>
                                    <span class="font-weight-bold ms-1">{{ $enrollments->count() }} courses enrolled</span>
                                </p>
                            </div>
                            <div class="col-lg-6 col-5 my-auto text-end">
                                <div class="dropdown float-lg-end pe-4">
                                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v text-secondary"></i>
                                    </a>
                                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                        <li><a class="dropdown-item border-radius-md" href="#">Export Course List</a></li>
                                        <li><a class="dropdown-item border-radius-md" href="#">Print Certificate</a></li>
                                        <li><a class="dropdown-item border-radius-md" href="{{ route('student.dashboard') }}">Back to Dashboard</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="coursesTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Course</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Enrolled Date</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $index => $enrollment)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <h6 class="mb-0 text-sm">{{ $index + 1 }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div class="avatar avatar-sm icon icon-shape bg-gradient-{{ ['primary', 'info', 'success', 'warning'][$index % 4] }} shadow text-center border-radius-md">
                                                            <i class="material-icons opacity-10 text-sm">menu_book</i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center ms-3">
                                                        <h6 class="mb-0 text-sm">{{ $enrollment->course->title }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $enrollment->course->course_code }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $enrollment->enrollment_date->format('M d, Y') }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $enrollment->enrollment_date->diffForHumans() }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $statusColor = match($enrollment->status ?? 'active') {
                                                        'completed' => 'success',
                                                        'suspended' => 'warning',
                                                        'inactive' => 'secondary',
                                                        default => 'info'
                                                    };
                                                @endphp
                                                <span class="badge badge-sm bg-gradient-{{ $statusColor }}">
                                                    {{ ucfirst($enrollment->status ?? 'Active') }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $progress = rand(20, 100); // This should come from actual progress calculation
                                                @endphp
                                                <div class="progress-wrapper w-75 mx-auto">
                                                    <div class="progress-info">
                                                        <div class="progress-percentage">
                                                            <span class="text-xs font-weight-bold">{{ $progress }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-{{ $progress >= 80 ? 'success' : ($progress >= 50 ? 'info' : 'warning') }}" 
                                                             role="progressbar" 
                                                             aria-valuenow="{{ $progress }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100" 
                                                             style="width: {{ $progress }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <a href="#" class="btn btn-primary btn-sm me-1" title="Continue Learning">
                                                        <i class="material-icons text-sm">play_arrow</i>
                                                    </a>
                                                    <a href="#" class="btn btn-info btn-sm me-1" title="Course Details">
                                                        <i class="material-icons text-sm">visibility</i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <a class="btn btn-secondary btn-sm" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons text-sm">more_vert</i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="#">Download Materials</a></li>
                                                            <li><a class="dropdown-item" href="#">View Certificate</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="#">Unenroll Course</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#coursesTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[2, 'desc']], // Order by enrollment date
            columnDefs: [
                { targets: [0, 3, 4, 5], orderable: false },
                { targets: [4, 5], searchable: false }
            ],
            language: {
                search: "Search courses:",
                lengthMenu: "Show _MENU_ courses per page",
                info: "Showing _START_ to _END_ of _TOTAL_ courses",
                infoEmpty: "No courses found",
                infoFiltered: "(filtered from _MAX_ total courses)"
            }
        });
    });
</script>
@endsection