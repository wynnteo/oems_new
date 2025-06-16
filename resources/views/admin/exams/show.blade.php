@extends('layouts.master')

@section('title')
{{ $exam->title }} | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <!-- Exam Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <div>
                        <h5 class="text-capitalize mb-0">
                            <i class="material-icons opacity-10 me-2">assignment</i>
                            {{ $exam->title }}
                        </h5>
                        <small class="text-muted">Exam Code: {{ $exam->exam_code }}</small>
                    </div>
                    <div class="actions_item">
                        <a class="btn btn-info btn-sm" href="{{ route('questions.create', $exam->id) }}" title="Add Question">
                            <i class="material-icons">question_answer</i> Add Question
                        </a>
                        <a class="btn btn-warning btn-sm" href="{{ route('exams.edit', $exam->id) }}" title="Edit Exam">
                            <i class="material-icons">edit</i> Edit
                        </a>
                        <a class="btn btn-secondary btn-sm" href="{{ route('exams.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Statistics Cards -->
    <div class="row mt-4">
        <!-- Total Attempts -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">groups</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Attempts</p>
                        <h4 class="mb-0">{{ $totalPass + $totalFail }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Pass -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check_circle</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Pass</p>
                        <h4 class="mb-0">{{ $totalPass }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Fail -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">cancel</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Fail</p>
                        <h4 class="mb-0">{{ $totalFail }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pass Rate -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">percent</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Pass Rate</p>
                        <h4 class="mb-0">
                            {{ ($totalPass + $totalFail) > 0 ? round(($totalPass / ($totalPass + $totalFail)) * 100, 1) : 0 }}%
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Highest Mark -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">trending_up</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Highest Mark</p>
                        <h4 class="mb-0">{{ $highestMark ?? 'N/A' }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Average Mark -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">analytics</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Average Mark</p>
                        <h4 class="mb-0">{{ $averageMark ?? 'N/A' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Additional Statistics Cards Row -->
    <div class="row mt-4">
        <!-- Lowest Mark -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">trending_down</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Lowest Mark</p>
                        <h4 class="mb-0">{{ $lowestMark ?? 'N/A' }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Completed Attempts -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">task_alt</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Completed</p>
                        <h4 class="mb-0">{{ $completedAttempts }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- In Progress -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">pending</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">In Progress</p>
                        <h4 class="mb-0">{{ $inProgressAttempts }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Questions -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">quiz</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Questions</p>
                        <h4 class="mb-0">{{ $totalQuestions }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Average Rating -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">star</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Avg Rating</p>
                        <h4 class="mb-0">
                            @if($averageRating)
                                {{ $averageRating }}/5
                                <i class="material-icons text-warning" style="font-size: 1rem;">star</i>
                            @else
                                N/A
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Average Duration -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">timer</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Avg Duration</p>
                        <h4 class="mb-0">
                            @php
                                $completedExams = $studentExams->filter(function($se) {
                                    return $se->started_at && $se->completed_at;
                                });
                                
                                if ($completedExams->count() > 0) {
                                    $totalSeconds = 0;
                                    foreach ($completedExams as $se) {
                                        $totalSeconds += $se->started_at->diffInSeconds($se->completed_at);
                                    }
                                    $avgSeconds = $totalSeconds / $completedExams->count();
                                    $avgMinutes = round($avgSeconds / 60);
                                    echo $avgMinutes . 'm';
                                } else {
                                    echo 'N/A';
                                }
                            @endphp
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details Section -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6><i class="material-icons me-2">info</i>Exam Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Basic Information</h6>
                            
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Course:</label>
                                <p class="text-sm mb-0">{{ $exam->course->title }} ({{ $exam->course->course_code }})</p>
                            </div>
                            
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Exam Code:</label>
                                <p class="text-sm mb-0">{{ $exam->exam_code }}</p>
                            </div>
                            
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Title:</label>
                                <p class="text-sm mb-0">{{ $exam->title }}</p>
                            </div>
                            
                            @if($exam->description)
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Description:</label>
                                <p class="text-sm mb-0">{{ $exam->description }}</p>
                            </div>
                            @endif
                            
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Status:</label>
                                <span class="badge {{ $exam->status == 'available' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $exam->status)) }}
                                </span>
                            </div>

                            
                        </div>
                        
                        <!-- Timing & Settings -->
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Timing & Settings</h6>
                            
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Duration:</label>
                                <p class="text-sm mb-0">
                                    @if($exam->duration > 0)
                                        {{ $exam->duration }} {{ $exam->duration_unit }}
                                    @else
                                        No time limit
                                    @endif
                                </p>
                            </div>
                            
                            @if($exam->start_time)
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Start Time:</label>
                                <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($exam->start_time)->format('M d, Y H:i') }}</p>
                            </div>
                            @endif
                            
                            @if($exam->end_time)
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">End Time:</label>
                                <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($exam->end_time)->format('M d, Y H:i') }}</p>
                            </div>
                            @endif
                            
                            @if($exam->price)
                            <div class="info-item mb-3">
                                <label class="text-dark font-weight-bold text-sm">Price:</label>
                                <p class="text-sm mb-0">${{ number_format($exam->price, 2) }}</p>
                            </div>
                            @endif
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Question & Grade Settings -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6><i class="material-icons me-2">quiz</i>Question Settings</h6>
                </div>
                <div class="card-body">
                    @if($exam->number_of_questions)
                    <div class="info-item mb-3">
                        <label class="text-dark font-weight-bold text-sm">Total Questions:</label>
                        <p class="text-sm mb-0">{{ $exam->number_of_questions }}</p>
                    </div>
                    @endif
                    
                    @if($exam->passing_grade)
                    <div class="info-item mb-3">
                        <label class="text-dark font-weight-bold text-sm">Passing Grade:</label>
                        <p class="text-sm mb-0">{{ $exam->passing_grade }}%</p>
                    </div>
                    @endif
                    
                    <div class="info-item mb-3">
                        <label class="text-dark font-weight-bold text-sm">Question Options:</label>
                        <div class="mt-2">
                            @if($exam->randomize_questions)
                                <span class="badge bg-info me-1">Randomized</span>
                            @endif
                            @if($exam->pagination)
                                <span class="badge bg-info me-1">Paginated</span>
                            @endif
                            @if(!$exam->randomize_questions && !$exam->pagination)
                                <span class="text-muted">None</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="text-dark font-weight-bold text-sm">Result Display:</label>
                        <div class="mt-2">
                            @if($exam->review_questions)
                                <span class="badge bg-success me-1">Show Results</span>
                            @endif
                            @if($exam->show_answers)
                                <span class="badge bg-success me-1">Show Answers</span>
                            @endif
                            @if(!$exam->review_questions && !$exam->show_answers)
                                <span class="text-muted">Hidden</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="text-dark font-weight-bold text-sm">Retakes:</label>
                        <p class="text-sm mb-0">
                            @if($exam->retake_allowed)
                                @if($exam->number_retake == 0)
                                    Unlimited
                                @else
                                    {{ $exam->number_retake }} allowed
                                @endif
                            @else
                                Not allowed
                            @endif
                        </p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="text-dark font-weight-bold text-sm">Additional Features:</label>
                        <div class="mt-2">
                            @if($exam->allow_rating)
                                <span class="badge bg-warning">Rating Enabled</span>
                            @endif
                            @if($exam->ip_restrictions)
                                <span class="badge bg-danger">IP Restricted</span>
                            @endif
                            @if(!$exam->allow_rating && !$exam->ip_restrictions)
                                <span class="text-muted">None</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ratings & Feedback Table -->
    @if($exam->allow_rating && $ratings->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="material-icons me-2">star_rate</i>Student Ratings & Feedback</h6>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table" id="ratingstable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Student</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Rating</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Feedback</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ratings as $rating)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <div class="avatar-title bg-gradient-info rounded-circle text-white">
                                                        {{ strtoupper(substr($rating->student->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-sm">{{ $rating->student->name }}</h6>
                                                    <p class="text-xs text-muted mb-0">{{ $rating->student->student_code }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="material-icons text-{{ $i <= $rating->rating ? 'warning' : 'muted' }}" style="font-size: 1.2rem;">
                                                        {{ $i <= $rating->rating ? 'star' : 'star_border' }}
                                                    </i>
                                                @endfor
                                                <span class="ms-2 text-sm font-weight-bold">{{ $rating->rating }}/5</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($rating->feedback)
                                                <p class="text-sm mb-0" style="max-width: 300px;">{!! $rating->feedback !!}</p>
                                            @else
                                                <span class="text-muted text-xs">No feedback provided</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-xs">{{ $rating->created_at->format('M d, Y') }}</span><br>
                                            <span class="text-xs text-muted">{{ $rating->created_at->format('H:i') }}</span>
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

    <!-- Student Results Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="material-icons me-2">assignment_turned_in</i>Student Results</h6>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="material-icons">file_download</i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-action="csv">CSV</a></li>
                            <li><a class="dropdown-item" href="#" data-action="excel">Excel</a></li>
                            <li><a class="dropdown-item" href="#" data-action="pdf">PDF</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible text-white mx-4">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <span>{{ $message }}</span>
                        </div>
                    @endif
                    
                    @if($studentExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table" id="examresulttable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Student Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Started</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Completed</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Duration</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Score (%)</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Grade</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentExams as $studentExam)
                                    @php
                                        $result = $studentExam->examResult;
                                        $status = $result && $result->score >= $exam->passing_grade ? 'Pass' : ($result ? 'Fail' : 'Not Graded');
                                        $statusClass = $result
                                            ? ($result->score >= $exam->passing_grade ? 'bg-success' : 'bg-danger')
                                            : 'bg-secondary';
                                        
                                        // Calculate duration
                                        $duration = 'N/A';
                                        if ($studentExam->started_at && $studentExam->completed_at) {
                                            $diff = $studentExam->started_at->diff($studentExam->completed_at);
                                            $duration = $diff->format('%H:%I:%S');
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <div class="avatar-title bg-gradient-primary rounded-circle text-white">
                                                        {{ strtoupper(substr($studentExam->student->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                {{ $studentExam->student->student_code }}
                                            </div>
                                        </td>
                                        <td>{{ $studentExam->student->name }}</td>
                                        <td>{{ $studentExam->student->email ?? 'N/A' }}</td>
                                        <td>
                                            <span class="text-xs">{{ $studentExam->started_at->format('M d, Y') }}</span><br>
                                            <span class="text-xs text-muted">{{ $studentExam->started_at->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            @if($studentExam->completed_at)
                                                <span class="text-xs">{{ $studentExam->completed_at->format('M d, Y') }}</span><br>
                                                <span class="text-xs text-muted">{{ $studentExam->completed_at->format('H:i') }}</span>
                                            @else
                                                <span class="badge bg-warning">In Progress</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $duration }}</span>
                                        </td>
                                        <td>
                                            @if($result)
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2 font-weight-bold">{{ number_format($result->score, 1) }}%</span>
                                                    <div class="progress" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar bg-gradient-{{ $result->score >= $exam->passing_grade ? 'success' : 'danger' }}" 
                                                             style="width: {{ $result->score }}%"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td>
                                            @if($result)
                                                <a href="{{ route('results.view', $result->id) }}" class="btn btn-primary btn-sm" title="View Results">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                            @else
                                                <span class="text-muted text-xs">No Result</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="icon icon-lg icon-shape bg-gradient-secondary shadow text-center border-radius-xl mb-3 mx-auto" style="width: 80px; height: 80px;">
                            <i class="material-icons opacity-10" style="font-size: 2rem;">assignment</i>
                        </div>
                        <h5 class="text-muted">No Student Attempts Yet</h5>
                        <p class="text-sm text-muted">Students haven't taken this exam yet. Results will appear here once they start attempting.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #67748e;
    margin-bottom: 0.25rem;
}

.info-item p {
    color: #344767;
    font-weight: 500;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: 0.875rem;
    font-weight: 600;
}

.progress {
    border-radius: 3px;
}

.card-header.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
}

.actions_item .btn {
    margin-left: 0.5rem;
}

.badge {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .card-header.actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .actions_item {
        width: 100%;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .actions_item .btn {
        margin-left: 0;
        flex: 1;
        min-width: auto;
    }
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const table = $('#examresulttable').DataTable({
        columnDefs: [
            {
                orderable: false,
                targets: -1 // Make the Action column unsortable
            },
            {
                targets: 0,
                width: '120px' 
            },
            {
                targets: 1,
                width: '150px' 
            },
            {
                targets: 2,
                width: '150px' 
            },
            {
                targets: [3, 4],
                width: '100px' 
            },
            {
                targets: 5,
                width: '80px' 
            },
            {
                targets: 6,
                width: '100px' 
            },
            {
                targets: 7,
                width: '80px' 
            },
            {
                targets: -1,
                width: '80px'
            }
        ],
        order: [[3, 'desc']], // Order by Started date descending
        pageLength: 25,
        responsive: true,
        language: {
            search: "Search students:",
            lengthMenu: "Show _MENU_ students per page",
            info: "Showing _START_ to _END_ of _TOTAL_ student attempts",
            infoEmpty: "No student attempts found",
            emptyTable: "No students have attempted this exam yet"
        },
        layout: {
            top1Start: {
                buttons: [{
                    text: 'CSV', extend: 'csvHtml5',
                    exportOptions: { columns: ':visible:not(.not-export-col)' }
                }, {
                    text: 'Excel', extend: 'excelHtml5',
                    exportOptions: { columns: ':visible:not(.not-export-col)' }
                }, {
                    text: 'PDF', extend: 'pdfHtml5',
                    pageSize: 'A4',
                    exportOptions: { columns: ':visible:not(.not-export-col)' },
                    customize: function(doc) {
                        doc.title = 'Student Result | OEMS';
                        doc.styles.title = { fontSize: 14, bold: true, color: 'black', alignment: 'center' };
                        doc.content.forEach(function (item) {
                            if (item.table) {
                                item.table.widths = Array(item.table.body[0].length).fill('*');
                            }
                        });
                    }
                }, {
                    text: 'Print', extend: 'print',
                    exportOptions: { columns: ':visible:not(.not-export-col)' }
                }]
            }
        }
    });

    $('#ratingstable').DataTable({
        order: [[3, 'desc']], // Order by date descending
        pageLength: 10,
        responsive: true,
        language: {
            search: "Search ratings:",
            lengthMenu: "Show _MENU_ ratings per page",
            info: "Showing _START_ to _END_ of _TOTAL_ ratings",
            emptyTable: "No ratings available for this exam"
        }
    });

    // Handle export dropdown clicks
    $('.dropdown-item[data-action]').on('click', function(e) {
        e.preventDefault();
        const action = $(this).data('action');
        
        switch(action) {
            case 'csv':
                table.button('.buttons-csv').trigger();
                break;
            case 'excel':
                table.button('.buttons-excel').trigger();
                break;
            case 'pdf':
                table.button('.buttons-pdf').trigger();
                break;
        }
    });

    // Auto-refresh data every 30 seconds for live updates
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>
@endsection