@extends('layouts.master')

@section('title')
Students | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">New Student</h5>
                    <div class="actions_item">

                        <a class="btn btn-darken" href="{{ route('students.index') }}" title="Back">
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
                    <form action="{{ route('students.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Full Name:</strong>
                                    <input type="text" name="name" class="form-control" placeholder=""
                                        value="{{old('name')}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Email:</strong>
                                    <input type="email" name="email" class="form-control" placeholder=""
                                        value="{{old('email')}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Student Code:</strong>
                                    <input type="text" name="student_code" class="form-control" placeholder="Student Code"
                                        value="{{old('student_code')}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Date of Birth:</strong>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth') }}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Phone Number:</strong>
                                    <input type="text" name="phone_number" class="form-control" placeholder=""
                                        value="{{ old('phone_number') }}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Gender:</strong>
                                    <select name="gender" class="form-control">
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="M" {{ old('gender')=='M' ? 'selected' : '' }}>Male</option>
                                        <option value="F" {{ old('gender')=='F' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Address:</strong>
                                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 pb-2">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control">
                                        <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ old('status')=='inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
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

    @endsection