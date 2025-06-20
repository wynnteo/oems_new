@extends('layouts.master')

@section('title')
{{ $course->title }} | Course Details
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $course->title }}</h4>
                        <p class="text-sm mb-0 text-muted">{{ $course->course_code }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('courses.edit', $course->id) }}">
                            <i class="material-icons me-1">edit</i> Edit Course
                        </a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('courses.index') }}">
                            <i class="material-icons me-1">arrow_back</i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Students</p>
                        <h4 class="mb-0">{{ number_format($enrolments->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Exams</p>
                        <h4 class="mb-0">{{ number_format($exams->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">trending_up</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Course Revenue</p>
                        <h4 class="mb-0">${{ number_format($enrolments->count() * $course->price, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">schedule</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Duration</p>
                        <h4 class="mb-0">{{ $course->duration ?? 0 }}h</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Details Section -->
    <div class="row mb-4">
        <!-- Course Information -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="material-icons me-2">school</i>Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Info -->
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Course Name</label>
                                <p class="mb-0">{{ $course->title }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Course Code</label>
                                <p class="mb-0">{{ $course->course_code }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Price</label>
                                <p class="mb-0">${{ number_format($course->price, 2) }}</p>
                            </div>
                            @if($course->category)
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Category</label>
                                <p class="mb-0">{{ $course->category }}</p>
                            </div>
                            @endif
                        </div>
                        <!-- Additional Info -->
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Status</label>
                                <p class="mb-0">
                                    @if($course->is_active == 'active')
                                        <span class="badge bg-gradient-success">Active</span>
                                    @elseif($course->is_active == 'inactive')
                                        <span class="badge bg-gradient-danger">Inactive</span>
                                    @else
                                        <span class="badge bg-gradient-secondary">Draft</span>
                                    @endif
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Featured</label>
                                <p class="mb-0">
                                    @if($course->is_featured)
                                        <span class="badge bg-gradient-warning">Featured</span>
                                    @else
                                        <span class="badge bg-gradient-secondary">Not Featured</span>
                                    @endif
                                </p>
                            </div>
                            @if($course->difficulty_level)
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Difficulty Level</label>
                                <p class="mb-0">
                                    <span class="badge 
                                        @if($course->difficulty_level == 'beginner') bg-gradient-success
                                        @elseif($course->difficulty_level == 'intermediate') bg-gradient-warning
                                        @else bg-gradient-danger
                                        @endif">
                                        {{ ucfirst($course->difficulty_level) }}
                                    </span>
                                </p>
                            </div>
                            @endif
                            @if($course->instructor)
                            <div class="info-item mb-3">
                                <label class="form-label text-dark font-weight-bold">Instructor</label>
                                <p class="mb-0">{{ $course->instructor }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($course->description)
                    <div class="mt-4">
                        <label class="form-label text-dark font-weight-bold">Description</label>
                        <div class="card bg-gray-100">
                            <div class="card-body">
                                <p class="mb-0">{{ $course->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($course->tags)
                    <div class="mt-4">
                        <label class="form-label text-dark font-weight-bold">Tags</label>
                        <div class="mt-2">
                            @foreach(explode(',', $course->tags) as $tag)
                                <span class="badge bg-gradient-info me-2">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Course Thumbnail & Additional Info -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="material-icons me-2">image</i>Course Media</h5>
                </div>
                <div class="card-body text-center">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                             class="img-fluid rounded mb-3" 
                             alt="{{ $course->title }}" 
                             style="max-height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-gray-200 rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                            <i class="material-icons text-muted" style="font-size: 48px;">image</i>
                        </div>
                    @endif
                    
                    @if($course->video_url)
                    <div class="mt-3">
                        <a href="{{ $course->video_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="material-icons me-1">play_circle</i>View Course Video
                        </a>
                    </div>
                    @endif

                    <div class="mt-4">
                        <div class="row text-center">
                            @if($course->language)
                            <div class="col-6">
                                <p class="text-sm mb-1 text-muted">Language</p>
                                <h6 class="mb-0">{{ $course->language }}</h6>
                            </div>
                            @endif
                            <div class="col-6">
                                <p class="text-sm mb-1 text-muted">Created</p>
                                <h6 class="mb-0">{{ $course->created_at->format('M d, Y') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students and Exams Tables -->
    <div class="row">
        <!-- Enrolled Students -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="material-icons me-2">people</i>
                        Enrolled Students
                        <span class="badge bg-gradient-primary ms-2">{{ $enrolments->count() }}</span>
                    </h5>
                    @if($enrolments->count() > 0)
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="material-icons me-1">more_vert</i>Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="material-icons me-2">download</i>Export List</a></li>
                            <li><a class="dropdown-item" href="#"><i class="material-icons me-2">email</i>Send Notification</a></li>
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="card-body px-0 pb-2">
                    @if($enrolments->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="enrolmenttable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Student</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Student ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Enrolled Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrolments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $enrollment->student->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $enrollment->student->student_code }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs mb-0">{{ $enrollment->student->email }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs mb-0">{{ $enrollment->enrollment_date->format('M d, Y H:i') }}</p>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('students.show', $enrollment->student->id) }}">
                                                    <i class="material-icons me-2">visibility</i>View Student
                                                </a></li>
                                                <li><a class="dropdown-item border-radius-md text-danger" href="#" onclick="unenrollStudent({{ $enrollment->id }}, {{ $enrollment->student->id }})">
                                                    <i class="material-icons me-2">person_remove</i>Unenroll
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="material-icons text-muted" style="font-size: 48px;">people_outline</i>
                        <h6 class="text-muted mt-2">No students enrolled yet</h6>
                        <p class="text-xs text-muted">Students will appear here once they enroll in this course.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Course Exams -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="material-icons me-2">assignment</i>
                        Course Exams
                        <span class="badge bg-gradient-success ms-2">{{ $exams->count() }}</span>
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('exams.create') }}?course_id={{ $course->id }}" class="btn btn-primary btn-sm">
                            <i class="material-icons me-1">add</i>Create Exam
                        </a>
                        @if($exams->count() > 0)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="material-icons me-1">more_vert</i>Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="material-icons me-2">download</i>Export Results</a></li>
                                <li><a class="dropdown-item" href="#"><i class="material-icons me-2">analytics</i>View Analytics</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if($exams->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="examtable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Exam Title</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date & Time</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Duration</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Attempts</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exams as $exam)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $exam->title }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($exam->description, 50) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $exam->start_time->format('M d, Y') }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $exam->start_time->format('H:i A') }}</p>
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-info">{{ $exam->formatDuration() }}</span>
                                    </td>
                                    <td>
                                        @if($exam->status == 'available')
                                            <span class="badge bg-gradient-success">Available</span>
                                        @else
                                            <span class="badge bg-gradient-warning">Not Available</span>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $exam->attempts_count ?? 0 }}</p>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.show', $exam->id) }}">
                                                    <i class="material-icons me-2">visibility</i>View Exam
                                                </a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.edit', $exam->id) }}">
                                                    <i class="material-icons me-2">edit</i>Edit Exam
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item border-radius-md text-danger" href="#" onclick="deleteExam({{ $exam->id }})">
                                                    <i class="material-icons me-2">delete</i>Delete Exam
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="material-icons text-muted" style="font-size: 48px;">assignment_outlined</i>
                        <h6 class="text-muted mt-2">No exams created yet</h6>
                        <p class="text-xs text-muted">Create your first exam for this course to get started.</p>
                        <a href="{{ route('exams.create') }}?course_id={{ $course->id }}" class="btn btn-primary btn-sm">
                            <i class="material-icons me-1">add</i>Create First Exam
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize DataTables only if tables have data
        if ($('#enrolmenttable tbody tr').length > 0) {
            $('#enrolmenttable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[3, 'desc']], // Sort by enrollment date
                columnDefs: [
                    { targets: -1, orderable: false } // Disable sorting for action column
                ]
            });
        }

        if ($('#examtable tbody tr').length > 0) {
            $('#examtable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[1, 'desc']], // Sort by date
                columnDefs: [
                    { targets: -1, orderable: false } // Disable sorting for action column
                ]
            });
        }
    });

    function unenrollStudent(enrollmentId, studentId) {
        if (confirm('Are you sure you want to unenroll this student from this course?')) {
            $.ajax({
                url: `/admin/students/${studentId}/unenroll/${enrollmentId}`,
                type: 'GET',
                beforeSend: function() {
                    $(`[onclick*="unenrollStudent(${enrollmentId}, ${studentId})"]`).prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    // Remove the table row
                    $(`tr:has([onclick*="unenrollStudent(${enrollmentId}, ${studentId})"])`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Update student count in statistics and table header
                        const currentCount = parseInt($('.badge.bg-gradient-primary').text());
                        $('.badge.bg-gradient-primary').text(currentCount - 1);
                        $('h4:contains("' + currentCount + '")').text(currentCount - 1);
                        
                        // Reinitialize DataTable if needed
                        if ($('#enrolmenttable tbody tr').length > 0) {
                            $('#enrolmenttable').DataTable().draw();
                        }
                    });
                    
                    showNotification('Student unenrolled successfully!', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error unenrolling student:', error);
                    showNotification('Failed to unenroll student. Please try again.', 'error');
                },
                complete: function() {
                    $(`[onclick*="unenrollStudent(${enrollmentId}, ${studentId})"]`).prop('disabled', false).html('<i class="material-icons me-2">person_remove</i>Unenroll');
                }
            });
        }
    }

    function deleteExam(examId) {
        if (confirm('Are you sure you want to delete this exam? This action cannot be undone.')) {
            $.ajax({
                url: `/admin/exams/${examId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $(`[onclick*="deleteExam(${examId})"]`).prop('disabled', true).text('Deleting...');
                },
                success: function(response) {
                    // Remove the table row
                    $(`tr:has([onclick*="deleteExam(${examId})"])`).fadeOut(300, function() {
                        $(this).remove();

                        // Update exam count in statistics and table header
                        const currentCount = parseInt($('.badge.bg-gradient-success').text());
                        $('.badge.bg-gradient-success').text(currentCount - 1);
                        $('h4:contains("' + currentCount + '")').text(currentCount - 1);

                        // Reinitialize DataTable if needed
                        if ($('#examtable tbody tr').length > 0) {
                            $('#examtable').DataTable().draw();
                        }
                    });

                    showNotification('Exam deleted successfully!', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting exam:', error);
                    showNotification('Failed to delete exam. Please try again.', 'error');
                },
                complete: function() {
                    $(`[onclick*="deleteExam(${examId})"]`).prop('disabled', false).html('<i class="material-icons me-2">delete</i>Delete Exam');
                }
            });
        }
    }

    // Notification function
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" role="alert" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut(300, function() { $(this).remove(); });
        }, 5000);
    }
</script>
@endsection