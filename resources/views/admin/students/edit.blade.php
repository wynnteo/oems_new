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
                    <h5 class="text-capitalize">Edit Student</h5>
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
                    <form action="{{ route('students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Full Name:</strong>
                                    <input type="text" name="name" class="form-control" placeholder="Student Full Name"
                                        value="{{ $student->name }}" required>
                                </div>
                            </div>
                            
                            <!-- Student Code -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Student Code:</strong>
                                    <input type="text" name="student_code" class="form-control" placeholder="Student Code"
                                        value="{{ $student->student_code }}" required>
                                    <small class="form-text text-muted">Must be unique for each student</small>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Email:</strong>
                                    <input type="email" name="email" class="form-control" placeholder="student@example.com"
                                        value="{{ $student->email }}" required>
                                </div>
                            </div>
                            
                            <!-- Phone Number -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Phone Number:</strong>
                                    <input type="text" name="phone_number" class="form-control" placeholder="Phone Number"
                                        value="{{ $student->phone_number }}">
                                </div>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Date of Birth:</strong>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ $student->date_of_birth }}">
                                </div>
                            </div>
                            
                            <!-- Gender -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Gender:</strong>
                                    <select name="gender" class="form-control" required>
                                        <option value="" disabled>Select Gender</option>
                                        <option value="M" {{ $student->gender == 'M' ? 'selected' : '' }}>Male</option>
                                        <option value="F" {{ $student->gender == 'F' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $student->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Address:</strong>
                                    <textarea class="form-control" name="address" rows="3"
                                        placeholder="Student address">{{ $student->address }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center pt-3">
                                <button type="submit" class="btn bg-gradient-dark btn-lg px-5">
                                    <i class="material-icons">update</i> Update Student
                                </button>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-info btn-lg px-5 ms-3">
                                    <i class="material-icons">visibility</i> View Student
                                </a>
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
        // Form validation enhancement
        const form = document.querySelector('form');
        const requiredFields = form.querySelectorAll('[required]');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
        
        // Remove invalid class on input
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
        
        // Confirmation before leaving if form has changes
        let formChanged = false;
        const inputs = form.querySelectorAll('input, select, textarea');
        const originalValues = {};
        
        // Store original values
        inputs.forEach(input => {
            originalValues[input.name] = input.value;
        });
        
        // Track changes
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value !== originalValues[this.name]) {
                    formChanged = true;
                } else {
                    // Check if any field is still changed
                    formChanged = Array.from(inputs).some(inp => 
                        inp.value !== originalValues[inp.name]
                    );
                }
            });
        });
        
        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        
        // Don't warn when submitting
        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });
</script>
@endsection