@extends('layouts.master')

@section('title')
Students | Admin Panel
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModal">Delete Record</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="student_delete_modal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    Are you sure you want to delete?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enroll Course Modal -->
<div class="modal fade" id="addcoursemodal" tabindex="-1" aria-labelledby="addcoursemodal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Course Enrollment</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add_course_modal" method="POST" action="{{ route('students.enroll', $student->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Search Course:</strong>
                                <select style="width:100%" id="coursesearchddl" name="courseList[]"
                                    multiple="multiple">
                                    <!-- Options will be dynamically populated -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-darken">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Students</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('students.edit', $student->id) }}" title="Edit Student">
                            <i class="material-icons">edit</i> Edit Student
                        </a>
                        <a class="btn btn-darken" href="{{ route('students.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>

                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible text-white mx-3 mt-3">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <span>{{ $message }}</span>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><i class="material-icons me-2">person</i> Student Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Student Name:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <strong>{{ $student->name }}</strong>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Student ID:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->student_code }}
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Gender:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ ucfirst($student->gender) }}
                                    </div>
                                </div>
                                
                                @if($student->date_of_birth)
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Date of Birth:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') }}
                                    </div>
                                </div>
                                @endif
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Status:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        @if($student->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($student->address)
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><i class="material-icons me-2">location_on</i> Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->email }}
                                    </div>
                                </div>
                                @if($student->phone_number)
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Phone Number:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->phone_number }}
                                    </div>
                                </div>
                                @endif
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Address:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->address }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Registered:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->created_at->format('M d, Y H:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-body px-0 pb-2">
                    <!-- Course Enrolled -->
                    <div class="table-title-div actions">
                        <h5><i class="material-icons me-2">school</i> Course Enrolled ({{ $student->enrollments()->count() }})</h5>
                        <div class="actions_item">
                            <a class="btn btn-darken" href="#" title="Enroll Course" data-bs-toggle="modal" data-bs-target="#addcoursemodal">
                                <i class="material-icons">add</i> Enroll Course
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive pb-5">
                        <table class="table" id="enrolmenttable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Course Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Course Title</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Enrolled At</th>  
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($student->enrollments()->with('course')->get() as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->course->course_code }}</td>
                                    <td>{{ $row->course->title }}</td>
                                    <td>{{ $row->enrollment_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($row->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($row->status ?? 'Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('courses.show', $row->course->id) }}"> <i class="material-icons">remove_red_eye</i> View Course</a></li>
                                                <li><a class="dropdown-item border-radius-md text-danger" href="#"
                                                    onclick="unenrollStudent({{ $row->id }}, {{ $student->id }})"> <i class="material-icons">person_remove</i> Unenroll</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Attempted Exams -->
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">assignment</i> Attempted Exams ({{ $student->studentExams()->count() }})</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="attemptedexamtable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Title</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Course</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Started At</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Completed At</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Score</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Result</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($student->studentExams()->with(['exam.course', 'examResult'])->get() as $row)
                                @php
                                    $result = $row->examResult;
                                    $status = $result && $result->score >= $row->exam->passing_grade ? 'Pass' : ($result ? 'Fail' : 'Pending');
                                    $statusClass = $result
                                        ? ($result->score >= $row->exam->passing_grade ? 'bg-success' : 'bg-danger')
                                        : 'bg-warning';
                                @endphp
                                <tr> 
                                    <td></td>
                                    <td>{{ $row->exam->exam_code }}</td>
                                    <td>{{ $row->exam->title }}</td>
                                    <td>{{ $row->exam->course->title ?? 'N/A' }}</td>
                                    <td>{{ $row->started_at ? $row->started_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $row->completed_at ? $row->completed_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        @if ($result)
                                            {{ $result->score }}/{{ $row->exam->total_marks ?? 100 }}
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.show', $row->exam->id) }}"> <i class="material-icons">remove_red_eye</i> View Exam</a></li>
                                                @if($result)
                                                    <li><a class="dropdown-item border-radius-md" href="#"> <i class="material-icons">assessment</i> View Result</a></li>
                                                @endif
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

        $('#attemptedexamtable').DataTable({
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

        $('#coursesearchddl').select2({
            placeholder: "Select courses",
            multiple: true,
            width: '100%',
            ajax: {
                url: '{{ route("courses.search") }}',
                dataType: 'json',
                type: 'GET',
                processResults: function(data) {
                    return {
                        results: data.map(function(course) {
                            return { id: course.id, text: course.title };
                        })
                    };
                },
                cache: true
            }
        });
    });

    function unenrollStudent(enrollmentId, studentId) {
        if (confirm('Are you sure you want to unenroll this student from the course?')) {
            $.ajax({
                url: `/admin/students/${studentId}/unenroll/${enrollmentId}`,
                type: 'GET',
                beforeSend: function() {
                    $(`[onclick*="unenrollStudent(${enrollmentId}, ${studentId})"]`).prop('disabled', true).text('Processing...');
                },
                success: function(response) {
                    $(`tr:has([onclick*="unenrollStudent(${enrollmentId}, ${studentId})"])`).fadeOut(300, function() {
                        $(this).remove();
                        const currentCount = parseInt($('.table-title-div h5:contains("Course Enrolled")').text().match(/\((\d+)\)/)[1]);
                        $('.table-title-div h5:contains("Course Enrolled")').html(`<i class="material-icons me-2">school</i> Course Enrolled (${currentCount - 1})`);
                        $('#enrolmenttable').DataTable().draw();
                    });
                    
                    showNotification('Student unenrolled successfully!', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error unenrolling student:', error);
                    showNotification('Failed to unenroll student. Please try again.', 'error');
                },
                complete: function() {
                    $(`[onclick*="unenrollStudent(${enrollmentId}, ${studentId})"]`).prop('disabled', false).html('<i class="material-icons">person_remove</i> Unenroll');
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