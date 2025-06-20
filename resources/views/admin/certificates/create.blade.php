@extends('layouts.master')

@section('title')
Certificates | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">New Certificate</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('certificates.index') }}" title="Back">
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
                    <form action="{{ route('certificates.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Student -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Student:</strong>
                                    <select name="student_id" id="student_id" class="form-control" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->name }} ({{ $student->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Course -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Course:</strong>
                                    <select name="course_id" id="course_id" class="form-control" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_code }} - {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Exam -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Exam (Optional):</strong>
                                    <select name="exam_id" id="exam_id" class="form-control">
                                        <option value="">Select Exam</option>
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
                                                {{ $exam->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Optional - Leave blank if not exam-based</small>
                                </div>
                            </div>
                            
                            <!-- Score -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Score:</strong>
                                    <input type="number" step="0.01" min="0" max="100" name="score" class="form-control" 
                                           placeholder="85.50" value="{{ old('score') }}" required>
                                    <small class="form-text text-muted">Score percentage (0-100)</small>
                                </div>
                            </div>
                            
                            <!-- Issue Date -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Issue Date:</strong>
                                    <input type="date" name="issued_at" class="form-control" 
                                           value="{{ old('issued_at', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="generated" {{ old('status') == 'generated' ? 'selected' : '' }}>Generated</option>
                                        <option value="revoked" {{ old('status') == 'revoked' ? 'selected' : '' }}>Revoked</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Certificate Data (Additional Information) -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Additional Notes:</strong>
                                    <textarea class="form-control" name="certificate_data[notes]" rows="3"
                                        placeholder="Any additional information or notes for this certificate">{{ old('certificate_data.notes') }}</textarea>
                                    <small class="form-text text-muted">Optional additional information</small>
                                </div>
                            </div>
                            
                            <!-- Certificate Data - Honors/Distinction -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Honors/Distinction:</strong>
                                    <select name="certificate_data[distinction]" class="form-control">
                                        <option value="">No Distinction</option>
                                        <option value="pass" {{ old('certificate_data.distinction') == 'pass' ? 'selected' : '' }}>Pass</option>
                                        <option value="merit" {{ old('certificate_data.distinction') == 'merit' ? 'selected' : '' }}>Merit</option>
                                        <option value="distinction" {{ old('certificate_data.distinction') == 'distinction' ? 'selected' : '' }}>Distinction</option>
                                        <option value="high_distinction" {{ old('certificate_data.distinction') == 'high_distinction' ? 'selected' : '' }}>High Distinction</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Certificate Data - Completion Type -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Completion Type:</strong>
                                    <select name="certificate_data[completion_type]" class="form-control">
                                        <option value="course_completion" {{ old('certificate_data.completion_type') == 'course_completion' ? 'selected' : '' }}>Course Completion</option>
                                        <option value="exam_passed" {{ old('certificate_data.completion_type') == 'exam_passed' ? 'selected' : '' }}>Exam Passed</option>
                                        <option value="achievement" {{ old('certificate_data.completion_type') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                                        <option value="participation" {{ old('certificate_data.completion_type') == 'participation' ? 'selected' : '' }}>Participation</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center pt-3">
                                <button type="submit" class="btn bg-gradient-dark btn-lg px-5">
                                    <i class="material-icons">save</i> Create Certificate
                                </button>
                                <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-3">
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
$(document).ready(function() {
    // Dynamic course loading based on student selection
    $('#student_id').change(function() {
        var studentId = $(this).val();
        var courseSelect = $('#course_id');
        var examSelect = $('#exam_id');
        
        // Reset course and exam dropdowns
        courseSelect.html('<option value="">Select Course</option>');
        examSelect.html('<option value="">Select Exam</option>');
        
        if (studentId) {
            $.ajax({
                url: '{{ route("certificates.courses-by-student") }}',
                type: 'GET',
                data: { student_id: studentId },
                success: function(courses) {
                    $.each(courses, function(index, course) {
                        courseSelect.append('<option value="' + course.id + '">' + 
                                          course.course_code + ' - ' + course.title + '</option>');
                    });
                },
                error: function() {
                    console.log('Error loading courses');
                }
            });
        }
    });
    
    // Dynamic exam loading based on course selection
    $('#course_id').change(function() {
        var courseId = $(this).val();
        var examSelect = $('#exam_id');
        
        // Reset exam dropdown
        examSelect.html('<option value="">Select Exam</option>');
        
        if (courseId) {
            $.ajax({
                url: '{{ route("certificates.exams-by-course") }}',
                type: 'GET',
                data: { course_id: courseId },
                success: function(exams) {
                    $.each(exams, function(index, exam) {
                        examSelect.append('<option value="' + exam.id + '">' + 
                                        exam.title + '</option>');
                    });
                },
                error: function() {
                    console.log('Error loading exams');
                }
            });
        }
    });
    
    // Score validation
    $('input[name="score"]').on('input', function() {
        var score = parseFloat($(this).val());
        if (score < 0) {
            $(this).val(0);
        } else if (score > 100) {
            $(this).val(100);
        }
    });
    
    // Auto-suggest distinction based on score
    $('input[name="score"]').on('change', function() {
        var score = parseFloat($(this).val());
        var distinctionSelect = $('select[name="certificate_data[distinction]"]');
        
        if (score >= 85) {
            distinctionSelect.val('high_distinction');
        } else if (score >= 75) {
            distinctionSelect.val('distinction');
        } else if (score >= 65) {
            distinctionSelect.val('merit');
        } else if (score >= 50) {
            distinctionSelect.val('pass');
        } else {
            distinctionSelect.val('');
        }
    });
});
</script>
@endsection