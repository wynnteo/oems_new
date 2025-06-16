@extends('layouts.studentmaster')

@section('title', 'Exam Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('student.exams') }}">Exams</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Exam Details</li>
                </ol>
            </nav>
            <h5 class="mb-0">{{ $exam->title }}</h5>
            <p class="text-sm mb-0">{{ $exam->exam_code }} - {{ $exam->course->title }}</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('student.exams') }}" class="btn btn-outline-secondary">
                <i class="material-icons me-2">arrow_back</i>
                Back to Exams
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Exam Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Exam Information</h6>
                        @if($registration)
                            <span class="badge bg-gradient-success">Registered</span>
                        @else
                            <span class="badge bg-gradient-secondary">Not Registered</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Course</label>
                                <p class="text-sm mb-0">{{ $exam->course->title }}</p>
                                <small class="text-secondary">{{ $exam->course->course_code }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Exam Code</label>
                                <p class="text-sm mb-0">{{ $exam->exam_code }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Date & Time</label>
                                <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($exam->start_time)->format('l, M d, Y') }}</p>
                                <small class="text-secondary">{{ \Carbon\Carbon::parse($exam->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('g:i A') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Duration</label>
                                <p class="text-sm mb-0">{{ $exam->duration ?? 120 }} {{ $exam->duration_unit ?? 'minutes' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($exam->description)
                    <div class="info-item mb-3">
                        <label class="form-label text-sm font-weight-bold mb-1">Description</label>
                        <p class="text-sm mb-0">{{ $exam->description }}</p>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Questions</label>
                                <p class="text-sm mb-0">{{ $exam->number_of_questions ?? 'TBD' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Passing Grade</label>
                                <p class="text-sm mb-0">{{ $exam->passing_grade ?? 60 }}%</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Retakes Allowed</label>
                                <p class="text-sm mb-0">
                                    @if($exam->retake_allowed)
                                        <span class="text-success">Yes</span>
                                    @else
                                        <span class="text-warning">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Review Questions</label>
                                <p class="text-sm mb-0">
                                    @if($exam->review_questions)
                                        <span class="text-success">Allowed</span>
                                    @else
                                        <span class="text-warning">Not Allowed</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-sm font-weight-bold mb-1">Access Code Required</label>
                                <p class="text-sm mb-0">
                                    @if($exam->access_code)
                                        <span class="text-warning">Yes</span>
                                    @else
                                        <span class="text-success">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Status & Actions -->
        <div class="col-lg-4">
            <!-- Registration Status Card -->
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Registration Status</h6>
                </div>
                <div class="card-body">
                    @if($registration)
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md me-3">
                                <i class="material-icons opacity-10">check_circle</i>
                            </div>
                            <div>
                                <h6 class="mb-0">Registered</h6>
                                <p class="text-sm text-secondary mb-0">{{ \Carbon\Carbon::parse($registration->registered_at)->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        @php
                            $examTime = \Carbon\Carbon::parse($exam->start_time);
                            $canReschedule = $examTime->copy()->subHours(48)->isFuture();
                            $canCancel = $examTime->copy()->subHours(24)->isFuture();
                        @endphp

                        <div class="d-grid gap-2">
                            @if($canReschedule)
                                <a href="{{ route('student.exams.reschedule', $exam->id) }}" class="btn btn-warning btn-sm">
                                    <i class="material-icons me-1">schedule</i>
                                    Reschedule
                                </a>
                            @endif

                            @if($canCancel)
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="material-icons me-1">cancel</i>
                                    Cancel Registration
                                </button>
                            @endif

                            @if(!$canReschedule && !$canCancel)
                                <div class="alert alert-info text-sm mb-0">
                                    <i class="material-icons me-1">info</i>
                                    Modification deadline has passed
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon icon-shape bg-gradient-secondary shadow text-center border-radius-md me-3">
                                <i class="material-icons opacity-10">event_available</i>
                            </div>
                            <div>
                                <h6 class="mb-0">Not Registered</h6>
                                <p class="text-sm text-secondary mb-0">You can register for this exam</p>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary" onclick="scheduleExam({{ $exam->id }})">
                                <i class="material-icons me-1">add</i>
                                Register for Exam
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Capacity Info -->
            <!-- <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Capacity</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-sm">Registered</span>
                        <span class="text-sm font-weight-bold">{{ $totalRegistrations }} / {{ $capacity }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-gradient-info" role="progressbar" 
                             style="width: {{ ($totalRegistrations / $capacity) * 100 }}%"
                             aria-valuenow="{{ $totalRegistrations }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $capacity }}">
                        </div>
                    </div>
                    @if($totalRegistrations >= $capacity)
                        <small class="text-danger">This exam is full</small>
                    @endif
                </div>
            </div> -->

            <!-- Important Notes -->
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Important Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled text-sm mb-0">
                        <li class="mb-2">
                            <i class="material-icons text-warning me-2">schedule</i>
                            Registration closes 2 hours before exam
                        </li>
                        <li class="mb-2">
                            <i class="material-icons text-info me-2">edit</i>
                            Reschedule up to 48 hours before
                        </li>
                        <li class="mb-2">
                            <i class="material-icons text-danger me-2">cancel</i>
                            Cancel up to 24 hours before
                        </li>
                        <li class="mb-0">
                            <i class="material-icons text-success me-2">timer</i>
                            Join exam 15 minutes before start time
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Exam Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="material-icons me-2">warning</i>
                        Are you sure you want to cancel your registration for this exam?
                    </div>
                    
                    <div class="form-group">
                        <label for="cancellation_reason" class="form-label">Reason for Cancellation (Optional)</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" 
                                  placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Registration</button>
                    <button type="submit" class="btn btn-danger">Cancel Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Handle exam registration
    function scheduleExam(examId) {
        fetch(`/student/exams/${examId}/schedule`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while registering for the exam');
        });
    }

    // Handle cancellation
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/student/exams/{{ $exam->id }}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the registration');
        });
    });
</script>
@endsection