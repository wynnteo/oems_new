@extends('layouts.studentmaster')

@section('title', 'Course Details - ' . $course->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
            <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="{{ route('student.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="{{ route('student.courses') }}">My Courses</a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $course->title }}</li>
        </ol>
    </nav>

    <!-- Course Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $course->title }}</h4>
                            <p class="text-secondary mb-2">{{ $course->course_code }}</p>
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-gradient-{{ $course->difficulty_level === 'beginner' ? 'success' : ($course->difficulty_level === 'intermediate' ? 'warning' : 'danger') }} me-2">
                                    {{ ucfirst($course->difficulty_level) }}
                                </span>
                                <span class="badge bg-gradient-info me-2">{{ $course->category }}</span>
                                @if($course->language)
                                    <span class="badge bg-gradient-secondary">{{ $course->language }}</span>
                                @endif
                            </div>
                            <p class="text-sm mb-0">{{ $course->description }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="img-fluid rounded shadow" style="max-height: 150px;">
                            @else
                                <div class="bg-gradient-primary rounded shadow d-flex align-items-center justify-content-center" style="height: 150px; width: 100%;">
                                    <i class="material-icons text-white" style="font-size: 48px;">menu_book</i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Course Information</h6>
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="text-xs text-secondary mb-0">Duration</p>
                            <p class="text-sm font-weight-bold mb-0">
                                {{ $course->duration ? $course->duration . ' hours' : 'Self-paced' }}
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs text-secondary mb-0">Instructor</p>
                            <p class="text-sm font-weight-bold mb-0">{{ $course->instructor ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="text-xs text-secondary mb-0">Price</p>
                            <p class="text-sm font-weight-bold mb-0">${{ number_format($course->price, 2) }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs text-secondary mb-0">Enrolled</p>
                            <p class="text-sm font-weight-bold mb-0">{{ $enrollment->enrollment_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="text-xs text-secondary mb-1">Your Progress</p>
                            @php
                                $progress = rand(20, 100); // Replace with actual progress calculation
                            @endphp
                            <div class="progress mb-1">
                                <div class="progress-bar bg-gradient-{{ $progress >= 80 ? 'success' : ($progress >= 50 ? 'info' : 'warning') }}" 
                                     role="progressbar" 
                                     style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="text-xs mb-0">{{ $progress }}% Complete</p>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        @if($course->video_url)
                            <a href="{{ $course->video_url }}" target="_blank" class="btn btn-outline-info">
                                <i class="material-icons me-2">video_library</i>
                                Watch Video
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Content Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="courseTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="material-icons me-2">info</i>Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">
                                <i class="material-icons me-2">assignment</i>Exams ({{ $courseExams->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                                <i class="material-icons me-2">folder</i>Materials
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="courseTabContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-3">Course Description</h6>
                                    <p>{{ $course->description ?: 'No detailed description available for this course.' }}</p>
                                    
                                    @if($course->tags)
                                        <h6 class="mb-3 mt-4">Tags</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach(explode(',', $course->tags) as $tag)
                                                <span class="badge bg-gradient-secondary">{{ trim($tag) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-gradient-light">
                                        <div class="card-body">
                                            <h6 class="mb-3">Quick Stats</h6>
                                            <div class="row text-center">
                                                <div class="col-6 border-end">
                                                    <h5 class="text-info mb-0">{{ $courseExams->count() }}</h5>
                                                    <p class="text-xs mb-0">Exams</p>
                                                </div>
                                                <div class="col-6">
                                                    <h5 class="text-success mb-0">{{ $course->students->count() }}</h5>
                                                    <p class="text-xs mb-0">Students</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exams Tab -->
                        <div class="tab-pane fade" id="exams" role="tabpanel">
                            @if($courseExams->isEmpty())
                                <div class="text-center py-4">
                                    <i class="material-icons text-secondary" style="font-size: 48px;">assignment</i>
                                    <h6 class="mt-3">No Exams Available</h6>
                                    <p class="text-secondary">There are no exams currently available for this course.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Duration</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Marks</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Passing Grade</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courseExams as $exam)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div>
                                                                <div class="avatar avatar-sm icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                                                    <i class="material-icons opacity-10 text-sm">assignment</i>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex flex-column justify-content-center ms-3">
                                                                <h6 class="mb-0 text-sm">{{ $exam->title }}</h6>
                                                                <p class="text-xs text-secondary mb-0">{{ $exam->exam_code }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-xs font-weight-bold">{{ $exam->duration ?? 120 }} mins</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-xs font-weight-bold">{{ $exam->total_marks ?? 100 }}</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-xs font-weight-bold">{{ $exam->passing_grade ?? 70 }}%</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        @php
                                                            $isRegistered = $exam->registrations->where('student_id', $student['->id'] ?? 0)->isNotEmpty();
                                                            $isCompleted = $exam->registrations->where('student_id', $student->id ?? 0)->where('status', 'completed')->isNotEmpty();
                                                        @endphp
                                                        @if($isCompleted)
                                                            <span class="badge badge-sm bg-gradient-success">Completed</span>
                                                        @elseif($isRegistered)
                                                            <span class="badge badge-sm bg-gradient-info">Registered</span>
                                                        @else
                                                            <span class="badge badge-sm bg-gradient-secondary">Available</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        @if($isCompleted)
                                                            <a href="#" class="btn btn-info btn-sm" title="View Result">
                                                                <i class="material-icons text-sm">visibility</i>
                                                            </a>
                                                        @elseif($isRegistered)
                                                            <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-primary btn-sm" title="View Exam">
                                                                <i class="material-icons text-sm">info</i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('student.exams.schedule') }}?exam_id={{ $exam->id }}" class="btn btn-success btn-sm" title="Schedule Exam">
                                                                <i class="material-icons text-sm">schedule</i>
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

                        <!-- Materials Tab -->
                        <div class="tab-pane fade" id="materials" role="tabpanel">
                            <div class="text-center py-4">
                                <i class="material-icons text-secondary" style="font-size: 48px;">folder_open</i>
                                <h6 class="mt-3">Course Materials</h6>
                                <p class="text-secondary">Download and access your course materials here.</p>
                                <div class="row mt-4">
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <i class="material-icons text-primary mb-2" style="font-size: 32px;">picture_as_pdf</i>
                                                <h6>Course Handbook</h6>
                                                <a href="#" class="btn btn-outline-primary btn-sm">Download</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <i class="material-icons text-info mb-2" style="font-size: 32px;">slideshow</i>
                                                <h6>Presentation Slides</h6>
                                                <a href="#" class="btn btn-outline-info btn-sm">Download</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <i class="material-icons text-success mb-2" style="font-size: 32px;">code</i>
                                                <h6>Code Examples</h6>
                                                <a href="#" class="btn btn-outline-success btn-sm">Download</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection