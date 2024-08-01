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
                    <h5 class="text-capitalize">New Course</h5>
                    <div class="actions_item">

                        <a class="btn btn-darken" href="{{ route('courses.index') }}" title="Back">
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
                    <form action="{{ route('courses.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Course Code:</strong>
                                    <input type="text" name="course_code" class="form-control" placeholder="Course Code"
                                        value="{{old('course_code')}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Course Name:</strong>
                                    <input type="text" name="title" class="form-control" placeholder="Title"
                                        value="{{old('title')}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description:</strong>
                                    <textarea class="form-control" name="description"
                                        placeholder="">{{old('description')}}</textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 pb-2">
                                <div class="form-group">
                                    <strong>Price:</strong>
                                    <input type="number" step='0.01' name="price" class="form-control"
                                        placeholder="Put the price" value="{{old('price')}}">
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

    </script>
    @endsection