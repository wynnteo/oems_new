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
                            <!-- Full Name -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Full Name:</strong>
                                    <input type="text" name="name" class="form-control" placeholder="Student Full Name"
                                        value="{{old('name')}}" required>
                                </div>
                            </div>
                            
                            <!-- Student Code -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Student Code:</strong>
                                    <input type="text" name="student_code" class="form-control" placeholder="Student Code"
                                        value="{{old('student_code')}}" required>
                                    <small class="form-text text-muted">Must be unique for each student</small>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Email:</strong>
                                    <input type="email" name="email" class="form-control" placeholder="student@example.com"
                                        value="{{old('email')}}" required>
                                </div>
                            </div>
                            
                            <!-- Phone Number -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Phone Number:</strong>
                                    <input type="text" name="phone_number" class="form-control" placeholder="Phone Number"
                                        value="{{ old('phone_number') }}">
                                </div>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Date of Birth:</strong>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth') }}">
                                </div>
                            </div>
                            
                            <!-- Gender -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Gender:</strong>
                                    <select name="gender" class="form-control" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="M" {{ old('gender')=='M' ? 'selected' : '' }}>Male</option>
                                        <option value="F" {{ old('gender')=='F' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Address:</strong>
                                    <textarea class="form-control" name="address" rows="3"
                                        placeholder="Student address">{{ old('address') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center pt-3">
                                <button type="submit" class="btn bg-gradient-dark btn-lg px-5">
                                    <i class="material-icons">save</i> Create Student
                                </button>
                                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                                    <i class="material-icons">cancel</i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate student code suggestion
        const studentCodeInput = document.querySelector('input[name="student_code"]');
        const nameInput = document.querySelector('input[name="name"]');
        
        if (studentCodeInput && !studentCodeInput.value) {
            const currentYear = new Date().getFullYear();
            const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            studentCodeInput.placeholder = `STU${currentYear}${randomNum}`;
        }
        
        // Generate slug from name
        if (nameInput && studentCodeInput) {
            nameInput.addEventListener('input', function() {
                if (!studentCodeInput.value) {
                    const slug = this.value.toLowerCase()
                        .replace(/[^a-z0-9]+/g, '')
                        .substring(0, 6);
                    if (slug) {
                        const currentYear = new Date().getFullYear();
                        const randomNum = Math.floor(Math.random() * 100).toString().padStart(2, '0');
                        studentCodeInput.placeholder = `STU${currentYear}${slug.toUpperCase()}${randomNum}`;
                    }
                }
            });
        }
    });
</script>
@endsection