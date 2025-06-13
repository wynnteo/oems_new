@extends('layouts.master')

@section('title')
Courses | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Courses</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('courses.edit', $course->id) }}" title="Edit Course">
                            <i class="material-icons">edit</i> Edit Course
                        </a>

                        <a class="btn btn-darken" href="{{ route('courses.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><i class="material-icons me-2">school</i> Course Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Course Name:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                       <strong> {{ $course->title }} </strong>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Course Code:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $course->course_code }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Course Fee:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                         @money($course->price) 
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Status:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        @if($course->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Featured:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        @if($course->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @else
                                            <span class="badge bg-secondary">Not Featured</span>
                                        @endif
                                    </div>
                                </div>
                                @if($course->category)
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Category:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $course->category }}
                                    </div>
                                </div>
                                @endif
                                @if($course->difficulty_level)
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Difficulty Level:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ ucfirst($course->difficulty_level) }}
                                    </div>
                                </div>
                                @endif
                                @if($course->instructor)
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Instructor:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $course->instructor }}
                                    </div>
                                </div>
                                @endif
                                @if($course->duration)
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Duration:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $course->duration }} hours
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($course->description)
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><i class="material-icons me-2">description</i> Course Description</h5>
                            </div>
                            
                            <div class="card-body">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
                                @endif
                                <p>{{ $course->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-body px-0 pb-2">
                    <!-- Student Enrolled -->
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">school</i> Student Enrolled ({{ $enrolments->count() }})</h5>
                    </div>
                    <div class="table-responsive pb-5">
                        <table class="table" id="enrolmenttable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Student ID</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Student Name</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Email</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Enrolled At</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrolments as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->student->student_code }}</td>
                                    <td>{{ $row->student->name }}</td>
                                    <td>{{ $row->student->email }}</td>
                                    <td>{{ $row->enrollment_date->format('Y-m-d H:i')  }}</td>
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('students.show', $row->student->id) }}"> <i class="material-icons">remove_red_eye</i> View Student</a></li>
                                                <li><a class="dropdown-item border-radius-md text-danger" href="#"
                                                    onclick="unenrollStudent({{ $row->id }}, {{$row->student->id}})"> <i class="material-icons">person_remove</i> Unenroll</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Exams -->
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">assignment</i> Exams ({{ $exams->count() }})</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="examtable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Date</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Time</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Duration</th>    
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exams as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->start_time->format('Y-m-d') }}</td>
                                    <td>{{ $row->start_time->format('H:i A') }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>
                                        {{ $row->formatDuration() }}
                                    </td>
                                    <td>@if ($row->status == 'available')
                                        <span class="badge bg-success">Available</span>
                                        @else
                                            <span class="badge bg-warning">Not Available</span>
                                        @endif
                                    </td>
                                   
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.show', $row->id) }}"> <i class="material-icons">remove_red_eye</i> View</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.edit', $row->id) }}"> <i class="material-icons">edit</i> Edit</a></li>
                                                <li><a class="dropdown-item border-radius-md text-danger" href="#" onclick="deleteExam({{ $row->id }})"> <i class="material-icons">delete</i> Delete</a></li>
                                            </ul>
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
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#enrolmenttable').DataTable({
            columnDefs: [{
                orderable: false,
                render: DataTable.render.select(),
                targets: 0
            },
            {
                targets: 0,
                width: '50px' 
            },
            {
                targets: -1,
                width: '100px'
            }],
            order: [[1, 'asc']],
            select: {
                style: 'os',
                selector: 'td:first-child'
            }
        });

        $('#examtable').DataTable({
            columnDefs: [{
                orderable: false,
                render: DataTable.render.select(),
                targets: 0
            },
            {
                targets: 0,
                width: '50px' 
            },
            {
                targets: -1,
                width: '100px'
            }],
            order: [[1, 'asc']],
            select: {
                style: 'os',
                selector: 'td:first-child'
            }
        });
    });

    function unenrollStudent(enrollmentId, studentId) {
        if (confirm('Are you sure you want to unenroll this student?')) {
            $.ajax({
                url: `/admin/students/${studentId}/unenroll/${enrollmentId}`,
                type: 'GET',
                beforeSend: function() {
                    $(`[onclick*="unenrollStudent(${enrollmentId}, ${studentId})"]`).prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    $(`tr:has([onclick*="unenrollStudent(${enrollmentId}, ${studentId})"])`).fadeOut(300, function() {
                        $(this).remove();
                        const currentCount = parseInt($('.table-title-div h5:contains("Student Enrolled")').text().match(/\((\d+)\)/)[1]);
                        $('.table-title-div h5:contains("Student Enrolled")').html(`<i class="material-icons me-2">school</i> Student Enrolled (${currentCount - 1})`);
                        $('#enrolmenttable').DataTable().draw();
                    });
                    
                    // Show success message
                    showNotification('Student unenrolled successfully!', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error unenrolling student:', error);
                    showNotification('Failed to unenroll student. Please try again.', 'error');
                },
                complete: function() {
                    $(`[onclick*="unenrollStudent(${enrollmentId}, ${studentId})"]`).prop('disabled', false).html('*person_remove* Unenroll');
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
                    $(`tr:has([onclick*="deleteExam(${examId})"])`).fadeOut(300, function() {
                        $(this).remove();

                        const currentCount = parseInt($('.table-title-div h5:contains("Exams")').text().match(/\((\d+)\)/)[1]);
                        $('.table-title-div h5:contains("Exams")').html(`<i class="material-icons me-2">assignment</i> Exams (${currentCount - 1})`);

                        $('#examtable').DataTable().draw();
                    });

                    showNotification('Exam deleted successfully!', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting exam:', error);
                    if (xhr.status === 404) {
                        showNotification('Exam not found.', 'error');
                    } else {
                        showNotification('Failed to delete exam. Please try again.', 'error');
                    }
                },
                complete: function() {
                    // Reset button state
                    $(`[onclick*="deleteExam(${examId})"]`).prop('disabled', false).html('<i class="material-icons">delete</i> Delete');
                }
            });
        }
    }

    // Notification function
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
</script>
@endsection