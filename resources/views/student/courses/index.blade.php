@extends('layouts.studentmaster')

@section('title', 'Enrolled Courses')

@section('content')
<div class="container-fluid py-4">
    <h5 class="mb-4">Enrolled Courses</h5>

    <!-- Check if there are any courses -->
    @if($enrollments->isEmpty())
        <div class="alert alert-info" role="alert">
            You are not enrolled in any courses yet.
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px"></th>
                            <th style="width:150px">Course Code</th>
                            <th>Title</th>
                            <th style="width:150px">Enrolled Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row->course->course_code }}</td>
                                <td>{{ $row->course->title }}</td>
                                <td>{{ $row->enrollment_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
