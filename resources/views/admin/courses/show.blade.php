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
                                
                                <h5 class="card-title"><i class="material-icons me-2">person</i> Course Information</h5>
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
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <!-- Student Enrolled -->
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">school</i> Student Enrolled</h5>
                    </div>
                    <div class="table-responsive pb-5">
                        <table class="table" id="enrolmenttable">
                            <thead>
                                <tr>
                                    <th></th>
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
                                    <td>{{ $row->student->name }}</td>
                                    <td>{{ $row->student->email }}</td>
                                    <td>{{ $row->enrolled_at->format('Y-m-d H:i')  }}</td>
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

                    <!-- Exams -->
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">assignment</i> Exams</h5>
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
                                    @php
                                        // Split the start_time string
                                        $startDateTime = explode(' ', $row->start_time);
                                        $startDate = $startDateTime[0] ?? '-';
                                        $startTime = $startDateTime[1] ?? '-';
                                    @endphp
                                    <td>{{ $startDate }}</td>
                                    <td>{{ $startTime }}</td>
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
                                                <li><a class="dropdown-item border-radius-md" href="{{ route('exams.destroy', $row->id) }}" data-method="DELETE"> <i class="material-icons">delete</i> Delete</a></li>
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

            $('#examtable').DataTable({
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