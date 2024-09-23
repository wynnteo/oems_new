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
                    <h5 class="text-capitalize">Exams</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('exams.edit', $exam->id) }}" title="Edit Student">
                            <i class="material-icons">edit</i> Edit Exam
                        </a>
                        <a class="btn btn-darken" href="{{ route('exams.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row">
                        <!-- Total Pass -->
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">check_circle</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Pass</p>
                                        <h4 class="mb-0">{{ $totalPass }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Fail -->
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">cancel</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Fail</p>
                                        <h4 class="mb-0">{{ $totalFail }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Highest Mark -->
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">trending_up</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Highest Mark</p>
                                        <h4 class="mb-0">{{ $highestMark }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Lowest Mark -->
                        <div class="col-xl-3 col-sm-6">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">trending_down</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Lowest Mark</p>
                                        <h4 class="mb-0">{{ $lowestMark }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Results Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body px-0 pb-2">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible text-white">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <span>{{ $message }}</span>
                        </div>
                    @endif
                    <div class="table-title-div">
                        <h5><i class="material-icons me-2">assignment</i> Results</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="examresulttable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Student Code</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Start Date</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Completed Date</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Score(%)</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Grade</th>
                                    <th class="not-export-col text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentExams as $studentExam)
                                    @php
                                        $result = $studentExam->examResult;
                                        $status = $result && $result->score > $exam->passing_grade ? 'Pass' : ($result ? 'Fail' : 'Not Graded');
                                        $statusClass = $result
                                            ? ($result->score > $exam->passing_grade ? 'bg-success' : 'bg-danger')
                                            : 'bg-secondary';
                                    @endphp
                                    <tr>
                                        <td>{{ $studentExam->student->student_code }}</td>
                                        <td>{{ $studentExam->student->name }}</td>
                                        <td>
                                            {{ $studentExam->started_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td>
                                            {{ $studentExam->completed_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td>{{ $result ? $result->score : 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td>
                                            @if($result)
                                                <a href="{{ route('results.view', $result->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="material-icons">remove_red_eye</i> View
                                                </a>
                                            @else
                                                <span class="text-muted">No Result</span>
                                            @endif
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
            $('#examresulttable').DataTable({
                columnDefs: [
                    {
                        orderable: false,
                        targets: -1 // Make the Action column unsortable
                    },
                    {
                        targets: 0,
                        width: '150px' 
                    },
                    {
                        targets: 1,
                        width: '200px' 
                    },
                    {
                        targets: 2,
                        width: '250px' 
                    },
                    {
                        targets: 3,
                        width: '100px' 
                    },
                    {
                        targets: 4,
                        width: '100px' 
                    },
                    {
                        targets: -1,
                        width: '150px'
                    }
                ],
                order: [[1, 'asc']], // Order by Name ascending
                // Add other DataTables options if needed
            });
        });
    </script>
    @endsection