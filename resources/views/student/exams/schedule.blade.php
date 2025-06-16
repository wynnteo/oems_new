@extends('layouts.studentmaster')

@section('title', 'Schedule Exam')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h5 class="mb-0">Schedule Exam</h5>
            <p class="text-sm mb-0">Choose from available exam sessions for your enrolled courses</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('student.exams.index') }}" class="btn btn-outline-primary">
                <i class="material-icons me-2">arrow_back</i>
                Back to My Exams
            </a>
        </div>
    </div>

    <!-- Course Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="form-label text-sm font-weight-bold">Filter by Course</label>
                            <select class="form-select" id="courseFilter">
                                <option value="">All Enrolled Courses</option>
                                @foreach($enrolledCourses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }} ({{ $course->course_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm font-weight-bold">Filter by Date</label>
                            <input type="date" class="form-control" id="dateFilter" min="{{ date('Y-m-d') }}" value="{{ request('date') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Exams -->
    @if($availableExams->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="material-icons text-secondary" style="font-size: 64px;">event_busy</i>
                    <h4 class="mt-3">No Available Exams</h4>
                    <p class="text-secondary">There are currently no exam sessions available for scheduling.</p>
                    <p class="text-sm text-secondary">Please check back later or contact your instructor.</p>
                </div>
            </div>
        </div>
    @else
        @foreach($enrolledCourses as $course)
            @php
                $courseExams = $availableExams->where('course_id', $course->id);
            @endphp
            @if($courseExams->count() > 0)
                <div class="card mb-4 course-section" data-course-id="{{ $course->id }}">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-lg-8">
                                <h6 class="mb-0">{{ $course->title }}</h6>
                                <p class="text-sm mb-0 text-secondary">
                                    <i class="material-icons text-sm me-1">book</i>
                                    {{ $course->course_code }} • {{ $courseExams->count() }} exam sessions available
                                </p>
                            </div>
                            <div class="col-lg-4 text-end">
                                <span class="badge bg-gradient-info">{{ $course->enrollments_count ?? 0 }} students enrolled</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam Details</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Schedule</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Duration</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Availability</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseExams as $exam)
                                        @php
                                            $startTime = \Carbon\Carbon::parse($exam->start_time);
                                            $endTime = \Carbon\Carbon::parse($exam->end_time);
                                            $now = now();
                                            
                                            // Check if student is already registered
                                            $isRegistered = $exam->registrations->contains('student_id', auth()->id());
                                            
                                            // Check if exam is full (if there's a capacity limit)
                                            $registrationCount = $exam->registrations->count();
                                            $capacity = $exam->capacity ?? 50; // Default capacity
                                            $isFull = $registrationCount >= $capacity;
                                            
                                            // Check if registration is still open
                                            $registrationDeadline = $startTime->subHours(2); // Close registration 2 hours before
                                            $canRegister = $now->isBefore($registrationDeadline) && !$isRegistered && !$isFull;
                                            
                                            // Status logic
                                            if ($isRegistered) {
                                                $status = 'Registered';
                                                $statusClass = 'bg-gradient-success';
                                            } elseif ($isFull) {
                                                $status = 'Full';
                                                $statusClass = 'bg-gradient-secondary';
                                            } elseif ($now->isAfter($registrationDeadline)) {
                                                $status = 'Closed';
                                                $statusClass = 'bg-gradient-secondary';
                                            } else {
                                                $status = 'Available';
                                                $statusClass = 'bg-gradient-info';
                                            }
                                        @endphp
                                        <tr class="exam-row" data-date="{{ $startTime->format('Y-m-d') }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div class="avatar avatar-sm icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                            <i class="material-icons opacity-10 text-sm">assignment</i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center ms-3">
                                                        <h6 class="mb-0 text-sm">{{ $exam->title }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $exam->exam_code }}
                                                            @if($exam->access_code)
                                                                <span class="badge badge-sm bg-gradient-warning ms-1">Access Code Required</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $startTime->format('M d, Y') }}</p>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}
                                                    </p>
                                                    <p class="text-xs text-info mb-0">
                                                        <i class="material-icons text-xs me-1">schedule</i>
                                                        Registration closes {{ $registrationDeadline->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ $exam->duration }} 
                                                    {{ $exam->duration_unit ?? 'minutes' }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="progress-wrapper w-75 mx-auto">
                                                    <div class="progress-info">
                                                        <div class="progress-percentage">
                                                            <span class="text-xs font-weight-bold">{{ $registrationCount }}/{{ $capacity }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                             style="width: {{ ($registrationCount / $capacity) * 100 }}%" 
                                                             aria-valuenow="{{ $registrationCount }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="{{ $capacity }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm {{ $statusClass }}">{{ $status }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($isRegistered)
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <button class="btn btn-outline-danger btn-sm me-1" 
                                                                onclick="cancelRegistration({{ $exam->id }})"
                                                                title="Cancel Registration">
                                                            <i class="material-icons text-sm">cancel</i>
                                                        </button>
                                                        <button class="btn btn-info btn-sm" 
                                                                onclick="viewExamDetails({{ $exam->id }})"
                                                                title="View Details">
                                                            <i class="material-icons text-sm">info</i>
                                                        </button>
                                                    </div>
                                                @elseif($canRegister)
                                                    <button class="btn btn-success btn-sm" 
                                                            onclick="scheduleExam({{ $exam->id }})"
                                                            title="Schedule This Exam">
                                                        <i class="material-icons text-sm me-1">event</i>
                                                        Schedule
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                                        <i class="material-icons text-sm">block</i>
                                                        {{ $isFull ? 'Full' : 'Closed' }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>

<!-- Schedule Confirmation Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Confirm Exam Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="examDetails">
                    <!-- Exam details will be loaded here -->
                </div>
                <div class="alert alert-info mt-3">
                    <strong>Important:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Registration closes 2 hours before the exam starts</li>
                        <li>Make sure you're available at the scheduled time</li>
                        <li>You can cancel your registration if needed</li>
                        <li>Arrive 15 minutes early for the exam</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSchedule">Confirm Registration</button>
            </div>
        </div>
    </div>
</div>

<!-- Exam Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Exam Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="examDetailsContent">
                    <!-- Detailed exam information will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Course filter functionality
    $('#courseFilter').on('change', function() {
        const selectedCourse = $(this).val();
        filterExams();
    });
    
    // Date filter functionality
    $('#dateFilter').on('change', function() {
        filterExams();
    });
    
    function filterExams() {
        const selectedCourse = $('#courseFilter').val();
        const selectedDate = $('#dateFilter').val();
        
        $('.course-section').each(function() {
            const courseId = $(this).data('course-id');
            let showCourse = !selectedCourse || courseId == selectedCourse;
            
            if (showCourse && selectedDate) {
                // Check if any exam in this course matches the selected date
                let hasMatchingDate = false;
                $(this).find('.exam-row').each(function() {
                    const examDate = $(this).data('date');
                    if (examDate === selectedDate) {
                        hasMatchingDate = true;
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                showCourse = hasMatchingDate;
            } else if (selectedDate) {
                // Filter by date only
                $(this).find('.exam-row').each(function() {
                    const examDate = $(this).data('date');
                    if (examDate === selectedDate) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                // Show all exams in the course
                $(this).find('.exam-row').show();
            }
            
            if (showCourse) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});

function scheduleExam(examId) {
    // Fetch exam details and show confirmation modal
    $.get(`/student/exams/${examId}/details`, function(data) {
        $('#examDetails').html(`
            <div class="row">
                <div class="col-md-6">
                    <strong>Exam:</strong> ${data.title}<br>
                    <strong>Code:</strong> ${data.exam_code}<br>
                    <strong>Course:</strong> ${data.course.title}
                </div>
                <div class="col-md-6">
                    <strong>Date:</strong> ${data.formatted_date}<br>
                    <strong>Time:</strong> ${data.formatted_time}<br>
                    <strong>Duration:</strong> ${data.duration} ${data.duration_unit}
                </div>
            </div>
        `);
        
        $('#confirmSchedule').data('exam-id', examId);
        $('#scheduleModal').modal('show');
    }).fail(function() {
        alert('Error loading exam details. Please try again.');
    });
}

$('#confirmSchedule').on('click', function() {
    const examId = $(this).data('exam-id');
    
    // Disable button to prevent double submission
    $(this).prop('disabled', true).text('Registering...');
    
    $.post(`/student/exams/${examId}/schedule`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    }).done(function(response) {
        $('#scheduleModal').modal('hide');
        if (response.success) {
            alert('Successfully registered for the exam!');
            location.reload();
        } else {
            alert(response.message || 'Registration failed. Please try again.');
        }
    }).fail(function(xhr) {
        const response = xhr.responseJSON;
        alert(response?.message || 'Registration failed. Please try again.');
    }).always(function() {
        $('#confirmSchedule').prop('disabled', false).text('Confirm Registration');
    });
});

function cancelRegistration(examId) {
    if (confirm('Are you sure you want to cancel your registration for this exam?')) {
        $.post(`/student/exams/${examId}/cancel`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(function(response) {
            if (response.success) {
                alert('Registration cancelled successfully!');
                location.reload();
            } else {
                alert(response.message || 'Failed to cancel registration.');
            }
        }).fail(function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'Failed to cancel registration.');
        });
    }
}

function viewExamDetails(examId) {
    // Load detailed exam information
    $.get(`/student/exams/${examId}/full-details`, function(data) {
        $('#examDetailsContent').html(`
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">${data.title}</h6>
                    <p class="card-text">${data.description || 'No description available.'}</p>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Exam Code:</strong> ${data.exam_code}<br>
                            <strong>Course:</strong> ${data.course.title}<br>
                            <strong>Date & Time:</strong> ${data.formatted_date} at ${data.formatted_time}<br>
                            <strong>Duration:</strong> ${data.duration} ${data.duration_unit}
                        </div>
                        <div class="col-md-6">
                            <strong>Questions:</strong> ${data.number_of_questions || 'TBD'}<br>
                            <strong>Passing Grade:</strong> ${data.passing_grade}%<br>
                            <strong>Retakes Allowed:</strong> ${data.retake_allowed ? 'Yes' : 'No'}<br>
                            <strong>Review Questions:</strong> ${data.review_questions ? 'Yes' : 'No'}
                        </div>
                    </div>
                    
                    ${data.access_code ? '<div class="alert alert-warning mt-3"><strong>Note:</strong> This exam requires an access code.</div>' : ''}
                </div>
            </div>
        `);
        
        $('#detailsModal').modal('show');
    }).fail(function() {
        alert('Error loading exam details. Please try again.');
    });
}
</script>
@endsection