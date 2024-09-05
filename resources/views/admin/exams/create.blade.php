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
                        <div class="row">
                            <!-- Course -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Course:</strong>
                                    <select name="course_id" class="form-control">
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-xs-9 col-sm-9 col-md-9">
                                <div class="form-group">
                                    <strong>Title:</strong>
                                    <input type="text" name="title" class="form-control" placeholder="Exam Title"
                                        value="{{ old('title') }}">
                                </div>
                            </div>

                            <!-- Exam Code -->
                            <div class="col-xs-3 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <strong>Exam Code:</strong>
                                    <input type="text" name="exam_code" class="form-control" placeholder="Exam Code"
                                        value="{{ old('exam_code') }}">
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description:</strong>
                                    <textarea class="form-control" name="description" placeholder="">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Duration, Duration Unit, and Start Time -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Exam Duration:</strong>
                                    <input type="number" name="duration" class="form-control" placeholder="Duration"
                                        value="{{ old('duration') }}">
                                    <small><i>Set zero to disable time limit.</i></small>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Duration Unit:</strong>
                                    <select name="duration_unit" class="form-control">
                                        <option value="minutes" {{ old('duration_unit') == 'minutes' ? 'selected' : '' }}>Minutes</option>
                                        <option value="hours" {{ old('duration_unit') == 'hours' ? 'selected' : '' }}>Hours</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Exam Date Time:</strong>
                                    <input type="datetime-local" name="start_time" class="form-control"
                                        value="{{ old('start_time') }}">
                                    
                                </div>
                            </div>

                            <!-- Number of Questions -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Number of Questions:</strong>
                                    <input type="number" name="number_of_questions" class="form-control"
                                        placeholder="Number of Questions" value="{{ old('number_of_questions') }}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Randomize Question Order:</strong>
                                    <input type="checkbox" name="randomize_questions" {{ old('randomize_questions') ? 'checked' : '' }}>
                                    <br/>
                                    <small class="form-text text-muted"><i>Randomize the question order for each student.</i></small>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Retake Allowed:</strong>
                                    <input type="checkbox" name="retake_allowed" {{ old('retake_allowed') ? 'checked' : '' }}>
                                    <br/>
                                    <small class="form-text text-muted"><i>Allow students to retake the exam if needed.</i></small>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Number of Retakes:</strong>
                                    <input type="number" name="number_retake" class="form-control"
                                        placeholder="Number of Retakes" value="{{ old('number_retake') }}">
                                        <small class="form-text text-muted"><i>Specify the maximum number of times a student can retake the exam.</i></small>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Passing Percentage (%):</strong>
                                    <input type="number" step='0.01' name="passing_grade" class="form-control"
                                        placeholder="Passing Grade" value="{{ old('passing_grade') }}">
                                        <small class="form-text text-muted"><i>Enter the percentage or grade required to pass the exam.</i></small>
                                </div>
                            </div>

                            <!-- Randomize Questions, Review Questions, Show Answers -->
                            
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Show Results:</strong>
                                    <input type="checkbox" name="review_questions" {{ old('review_questions') ? 'checked' : '' }}>
                                    <br/>
                                    <small class="form-text text-muted"><i>The exam result will be shown to the students.</i></small>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Show Answers:</strong>
                                    <input type="checkbox" name="show_answers" {{ old('show_answers') ? 'checked' : '' }}>
                                    <br/>
                                    <small class="form-text text-muted"><i>The exam result with correct answers will be shown to the students.</i></small>
                                </div>
                            </div>
                            <!-- Status -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control">
                                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="not_available" {{ old('status') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Access Code -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Access Code:</strong>
                                    <input type="text" name="access_code" class="form-control" placeholder="Access Code"
                                        value="{{ old('access_code') }}">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Allow Rating:</strong>
                                    <input type="checkbox" name="allow_rating" {{ old('allow_rating') ? 'checked' : '' }}>
                                    <br/>
                                    <small class="form-text text-muted"><i>Enable students to rate the exam after complete.</i></small>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" class="btn bg-gradient-dark">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Add any specific JavaScript required for the form
</script>
@endsection
