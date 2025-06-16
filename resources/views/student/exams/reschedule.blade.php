@extends('layouts.studentmaster')

@section('title', 'Reschedule Exam')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="{{ route('student.exams') }}">Exams</a>
                    </li>
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="{{ route('student.exams.show', $exam->id) }}">{{ $exam->title }}</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Reschedule</li>
                </ol>
            </nav>
            <h5 class="mb-0">Reschedule Exam</h5>
            <p class="text-sm mb-0">{{ $exam->exam_code }} - {{ $exam->course->title }}</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-outline-secondary">
                <i class="material-icons me-2">arrow_back</i>
                Back to Details
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Current Registration Info -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Current Registration</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md me-3">
                            <i class="material-icons opacity-10">schedule</i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $exam->title }}</h6>
                            <p class="text-sm text-secondary mb-0">{{ $exam->exam_code }}</p>
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <label class="form-label text-sm font-weight-bold mb-1">Current Date & Time</label>
                        <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($exam->start_time)->format('l, M d, Y g:i A') }} <br/>-<br/>
                                {{ \Carbon\Carbon::parse($exam->end_time)->format('l, M d, Y g:i A') }}</p>
                    </div>

                    <div class="info-item mb-3">
                        <label class="form-label text-sm font-weight-bold mb-1">Registered On</label>
                        <p class="text-sm mb-0">{{ \Carbon\Carbon::parse($registration->registered_at)->format('M d, Y g:i A') }}</p>
                    </div>

                    <div class="alert alert-warning text-sm">
                        <i class="material-icons me-1">info</i>
                        Reschedule deadline: {{ \Carbon\Carbon::parse($exam->start_time)->subHours(48)->format('M d, Y g:i A') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Select New Exam Date</h6>
                </div>
                <div class="card-body">
                    @if($alternativeExams->count() > 0)
                        <form action="{{ route('student.exams.reschedule.process', $exam->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            
                            <div class="form-group mb-4">
                                <label for="reason" class="form-label">Reason for Reschedule</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" 
                                          placeholder="Please provide a reason for rescheduling..."></textarea>
                                <small class="text-muted">Optional but recommended for record keeping</small>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Available Alternative Dates</label>
                                <div class="row">
                                    @foreach($alternativeExams as $altExam)
                                        @php
                                            $registrationCount = \App\Models\ExamRegistration::where('exam_id', $altExam->id)->count();
                                            $capacity = $altExam->capacity ?? 50;
                                            $availableSpots = $capacity - $registrationCount;
                                        @endphp
                                        
                                        <div class="col-12 mb-3">
                                            <div class="card exam-option {{ $availableSpots <= 0 ? 'disabled' : '' }}">
                                                <div class="card-body p-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="new_exam_id" 
                                                               id="exam_{{ $altExam->id }}" value="{{ $altExam->id }}"
                                                               {{ $availableSpots <= 0 ? 'disabled' : '' }} required>
                                                        <label class="form-check-label w-100" for="exam_{{ $altExam->id }}">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1">{{ $altExam->title }}</h6>
                                                                    <p class="text-sm text-secondary mb-1">{{ $altExam->exam_code }}</p>
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="material-icons text-info me-1">event</i>
                                                                        <span class="text-sm">{{ \Carbon\Carbon::parse($altExam->start_time)->format('l, M d, Y') }}</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="material-icons text-info me-1">schedule</i>
                                                                        <span class="text-sm">{{ \Carbon\Carbon::parse($altExam->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($altExam->end_time)->format('g:i A') }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end">
                                                                    @if($availableSpots <= 0)
                                                                        <span class="badge bg-gradient-danger">Full</span>
                                                                    @elseif($availableSpots <= 5)
                                                                        <span class="badge bg-gradient-warning">{{ $availableSpots }} spots left</span>
                                                                    @else
                                                                        <span class="badge bg-gradient-success">{{ $availableSpots }} spots available</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-secondary">
                                        <i class="material-icons me-1">cancel</i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="material-icons me-1">schedule</i>
                                        Reschedule Exam
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="material-icons me-2">info</i>
                                <div>
                                    <h6 class="mb-1">No Alternative Dates Available</h6>
                                    <p class="mb-0">Unfortunately, there are no other available exam dates for this course that you can reschedule to.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-secondary">
                                <i class="material-icons me-1">arrow_back</i>
                                Back to Exam Details
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.exam-option {
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
}

.exam-option:hover:not(.disabled) {
    border-color: #fb6340;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.exam-option.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.exam-option input[type="radio"]:checked + label {
    background-color: rgba(251, 99, 64, 0.1);
}

.exam-option input[type="radio"]:checked ~ * {
    border-color: #fb6340;
}
</style>
@endsection

@section('scripts')
<script>
    // Add interactive behavior for exam selection
    document.querySelectorAll('.exam-option:not(.disabled)').forEach(function(option) {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio && !radio.disabled) {
                radio.checked = true;
                
                // Remove previous selection styling
                document.querySelectorAll('.exam-option').forEach(function(opt) {
                    opt.style.borderColor = 'transparent';
                });
                
                // Add selection styling
                this.style.borderColor = '#fb6340';
            }
        });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedExam = document.querySelector('input[name="new_exam_id"]:checked');
        if (!selectedExam) {
            e.preventDefault();
            alert('Please select a new exam date before proceeding.');
            return false;
        }
        
        return confirm('Are you sure you want to reschedule your exam to the selected date and time?');
    });
</script>
@endsection