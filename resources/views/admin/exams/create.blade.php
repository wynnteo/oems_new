@extends('layouts.master')

@section('title')
Exams | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">New Exam</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('exams.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body pb-2">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('exams.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Basic Information</h6>
                                <hr class="horizontal dark mt-0 mb-2">
                            </div>
                            
                            <!-- Course -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Course: <span class="text-danger">*</span></strong></label>
                                    <select name="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                                        <option value="">Select a course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Exam Code -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Exam Code: <span class="text-danger">*</span></strong></label>
                                    <input type="text" name="exam_code" class="form-control @error('exam_code') is-invalid @enderror" 
                                           placeholder="Enter unique exam code" value="{{ old('exam_code') }}" required>
                                    <small class="form-text text-muted">Must be unique identifier for the exam</small>
                                    @error('exam_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Exam Title: <span class="text-danger">*</span></strong></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                           placeholder="Enter exam title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Description:</strong></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" 
                                              rows="3" placeholder="Enter exam description (optional)">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Timing & Schedule Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Timing & Schedule</h6>
                                <hr class="horizontal dark mt-0 mb-2">
                            </div>

                            <!-- Duration -->
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Duration:</strong></label>
                                    <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" 
                                           placeholder="0" value="{{ old('duration', 0) }}" min="0">
                                    <small class="form-text text-muted">Set 0 to disable time limit</small>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Duration Unit -->
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Duration Unit: <span class="text-danger">*</span></strong></label>
                                    <select name="duration_unit" class="form-control @error('duration_unit') is-invalid @enderror" required>
                                        <option value="minutes" {{ old('duration_unit', 'minutes') == 'minutes' ? 'selected' : '' }}>Minutes</option>
                                        <option value="hours" {{ old('duration_unit') == 'hours' ? 'selected' : '' }}>Hours</option>
                                    </select>
                                    @error('duration_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Price:</strong></label>
                                    <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" 
                                           placeholder="0.00" value="{{ old('price') }}" min="0">
                                    <small class="form-text text-muted">Leave empty for free exam</small>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Start Time -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Start Date & Time:</strong></label>
                                    <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_time') }}">
                                    <small class="form-text text-muted">When the exam becomes available</small>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Time -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>End Date & Time:</strong></label>
                                    <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ old('end_time') }}">
                                    <small class="form-text text-muted">When the exam becomes unavailable (optional)</small>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Question Settings Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Question Settings</h6>
                                <hr class="horizontal dark mt-0 mb-2">
                            </div>

                            <!-- Number of Questions -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Number of Questions:</strong></label>
                                    <input type="number" name="number_of_questions" class="form-control @error('number_of_questions') is-invalid @enderror"
                                           placeholder="Total questions in exam" value="{{ old('number_of_questions') }}" min="1">
                                    <small class="form-text text-muted">Total number of questions to display</small>
                                    @error('number_of_questions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Passing Grade -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Passing Grade (%):</strong></label>
                                    <input type="number" step="0.01" name="passing_grade" class="form-control @error('passing_grade') is-invalid @enderror"
                                           placeholder="Minimum percentage to pass" value="{{ old('passing_grade') }}" min="0" max="100">
                                    <small class="form-text text-muted">Percentage required to pass the exam</small>
                                    @error('passing_grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Question Options -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Question Options:</strong></label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="randomize_questions" 
                                               id="randomize_questions" {{ old('randomize_questions') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="randomize_questions">
                                            Randomize Question Order
                                        </label>
                                        <small class="form-text text-muted d-block">Shuffle questions for each student</small>
                                    </div>
                                    
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="pagination" 
                                               id="pagination" {{ old('pagination') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pagination">
                                            Enable Pagination
                                        </label>
                                        <small class="form-text text-muted d-block">Show one question per page</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Result Display Options -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Result Display:</strong></label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="review_questions" 
                                               id="review_questions" {{ old('review_questions') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="review_questions">
                                            Show Results on Completion
                                        </label>
                                        <small class="form-text text-muted d-block">Display results after exam completion</small>
                                    </div>
                                    
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="show_answers" 
                                               id="show_answers" {{ old('show_answers') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_answers">
                                            Show Correct Answers
                                        </label>
                                        <small class="form-text text-muted d-block">Display correct answers with results</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Retake Settings Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Retake Settings</h6>
                                <hr class="horizontal dark mt-0 mb-2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="retake_allowed" 
                                               id="retake_allowed" {{ old('retake_allowed') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="retake_allowed">
                                            <strong>Allow Retakes</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">Enable students to retake the exam</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Number of Retakes:</strong></label>
                                    <input type="number" name="number_retake" class="form-control @error('number_retake') is-invalid @enderror"
                                           placeholder="Maximum retakes allowed" value="{{ old('number_retake', 0) }}" min="0">
                                    <small class="form-text text-muted">0 = unlimited retakes</small>
                                    @error('number_retake')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Security & Access Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Security & Access</h6>
                                <hr class="horizontal dark mt-0 mb-2">
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Status: <span class="text-danger">*</span></strong></label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="not_available" {{ old('status') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Access Code -->
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Access Code:</strong></label>
                                    <input type="text" name="access_code" class="form-control @error('access_code') is-invalid @enderror" 
                                           placeholder="Enter access code" value="{{ old('access_code') }}">
                                    <small class="form-text text-muted">Optional password for exam access</small>
                                    @error('access_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- IP Restrictions -->
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-control-label"><strong>Security Options:</strong></label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="ip_restrictions" 
                                               id="ip_restrictions" {{ old('ip_restrictions') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ip_restrictions">
                                            Enable IP Restrictions
                                        </label>
                                        <small class="form-text text-muted d-block">Restrict access by IP address</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Features Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Additional Features</h6>
                                <hr class="horizontal dark mt-0 mb-2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allow_rating" 
                                               id="allow_rating" {{ old('allow_rating') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_rating">
                                            <strong>Allow Student Ratings</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">Enable students to rate the exam after completion</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn bg-gradient-dark btn-lg px-5">
                                    <i class="material-icons">save</i> Create Exam
                                </button>
                                <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                                    <i class="material-icons">cancel</i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle retake number input based on retake allowed checkbox
        const retakeAllowedCheckbox = document.getElementById('retake_allowed');
        const numberRetakeInput = document.querySelector('input[name="number_retake"]');
        
        function toggleRetakeInput() {
            if (retakeAllowedCheckbox.checked) {
                numberRetakeInput.removeAttribute('disabled');
                numberRetakeInput.parentElement.style.opacity = '1';
            } else {
                numberRetakeInput.setAttribute('disabled', 'disabled');
                numberRetakeInput.parentElement.style.opacity = '0.5';
                numberRetakeInput.value = '0';
            }
        }
        
        retakeAllowedCheckbox.addEventListener('change', toggleRetakeInput);
        toggleRetakeInput(); // Initial call
        
        // Validate end time is after start time
        const startTimeInput = document.querySelector('input[name="start_time"]');
        const endTimeInput = document.querySelector('input[name="end_time"]');
        
        function validateEndTime() {
            if (startTimeInput.value && endTimeInput.value) {
                if (new Date(endTimeInput.value) <= new Date(startTimeInput.value)) {
                    endTimeInput.setCustomValidity('End time must be after start time');
                } else {
                    endTimeInput.setCustomValidity('');
                }
            }
        }
        
        startTimeInput.addEventListener('change', validateEndTime);
        endTimeInput.addEventListener('change', validateEndTime);
        
        // Auto-generate exam code if not provided
        const titleInput = document.querySelector('input[name="title"]');
        const examCodeInput = document.querySelector('input[name="exam_code"]');
        
        titleInput.addEventListener('blur', function() {
            if (!examCodeInput.value && titleInput.value) {
                const code = titleInput.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 10);
                examCodeInput.value = code + Math.floor(Math.random() * 1000);
            }
        });
    });
</script>
@endsection