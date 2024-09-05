@extends('layouts.studentmaster')

@section('title', 'Exams')

@section('content')
<div class="container-fluid py-4">
    <h5 class="mb-4">Exams</h5>

    <!-- Registered Exams Section -->
    <div class="card mb-4">
        <div class="card-header actions">
            <h5 class="mb-0">Upcoming Exams</h5>
            <div class="actions_item">
                <a class="btn btn-darken" href="" title="Schedule an Exam">
                    <i class="material-icons">add</i>  Schedule an Exam
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($registeredExams->isEmpty())
                <div class="alert alert-info" role="alert">
                    No registered exams found.
                </div>
            @else
                <table class="table table-striped" id="examtable">
                    <thead>
                        <tr>
                            <th style="width:150px">Exam Code</th>
                            <th>Title</th>
                            <th style="width:150px">Exam Date</th>
                            <th style="width:150px">Status</th>
                            <th style="width:150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registeredExams as $index => $exam)
                            <tr>
                                <td>{{ $exam->exam->exam_code }}</td>
                                <td>{{ $exam->exam->title }}</td>
                                <td>{{ $exam->exam->start_time }}</td>
                                <td>{{ $exam->status }}</td>
                                <td>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Completed Exams Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Completed Exams</h5>
        </div>
        <div class="card-body">
            @if($completedExams->isEmpty())
                <div class="alert alert-info" role="alert">
                    No completed exams found.
                </div>
            @else
                <table class="table table-striped" id="cexamtable">
                    <thead>
                        <tr>
                            <th style="width:150px">Exam Code</th>
                            <th>Title</th>
                            <th style="width:150px">Start At</th>
                            <th style="width:150px">Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedExams as $index => $exam)
                            <tr>
                                <td>{{ $exam->exam->exam_code }}</td>
                                <td>{{ $exam->exam->title }}</td>
                                <td>{{ $exam->started_at }}</td>
                                <td>{{ $exam->completed_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {
            $('#examtable').DataTable({
                columnDefs: [
                {
                    targets: 0,
                    width: '100px' 
                },
                {
                    targets: -1,
                    width: '100px'
                }]
            });

            $('#cexamtable').DataTable({
                columnDefs: [
                {
                    targets: 0,
                    width: '100px' 
                },
                {
                    targets: -1,
                    width: '100px'
                }]
            });
            
        });
    </script>
    @endsection