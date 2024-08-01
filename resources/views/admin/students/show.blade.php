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
                                        {{ $student->name }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Student Number:</strong>
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
                                        {{ $student->gender }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->email }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>DOB:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->date_of_birth }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-4">
                                        <strong>Contact:</strong>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        {{ $student->phone_number }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span>{{ $message }}</span>
                    </div>
                    @endif

                    <!-- Course Enrolled -->
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">school</i> Course Enrolled</h5>
                    </div>
                    <div class="table-responsive pb-5">
                        <table class="table" id="enrolmenttable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Course Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Course</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Enrolled At</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($student->courses()->get() as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->course_code }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td></td>
                                    <td>
                                        <div class="dropdown float-lg-end pe-4">
                                            <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-secondary"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                aria-labelledby="dropdownTable">
                                                <li><a class="dropdown-item border-radius-md"
                                                    href="{{ route('students.show', $row->id) }}"> <i class="material-icons">remove_red_eye</i> Unenroll</a></li>
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
                        <h5><i class="material-icons me-2">assignment</i> Attempted Exams</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="attemptedexamtable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam Date</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Exam</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Result</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($student->studentExams as $row)
                                <tr>
                                    <td></td>
                                    <td>{{ $row->exam->started_at ? $row->exam->started_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $row->exam->title }}</td>
                                    <td>@if ($row->completed_at)
                                        <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-warning">In Progress</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($studentExam->studentExamResult)
                                            {{ $row->examResult->score }}
                                        @else
                                            Not Available
                                        @endif
                                    </td>
                                    <td>
                                        
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
    @endsection


    @section('scripts')
    <script>
        $(document).ready(function () {
            $('#enrolmenttable').DataTable({
                columnDefs: [{
                    orderable: false,
                    render: DataTable.render.select(),
                    targets: 0
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
                }],
                order: [[1, 'asc']],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                }
            });

            // $('#enrolmenttable').on('click', '.deletebtn', function () {
            //     $action = $(this).attr("data-action");
            //     $('#student_delete_modal').attr('action', $action);
            //     $('#deletemodal').modal('show');
            // });
        });
    </script>
    @endsection